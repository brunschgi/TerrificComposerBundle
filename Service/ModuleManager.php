<?php
/*
 * This file is part of the Terrific Composer package.
 *
 * (c) Remo Brunschwiler <remo@terrifically.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terrific\ComposerBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Terrific\ComposerBundle\Entity\Module;
use Terrific\ComposerBundle\Entity\Template as ModuleTemplate;
use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * ModuleManager.
 */
class ModuleManager
{
    private $kernel;
    private $container;

    /**
     * Constructor.
     *
     * @param KernelInterface       $kernel       The kernel is used to parse bundle notation
     * @param ContainerInterface    $container    The container is used to load the managers lazily, thus avoiding a circular dependency
     */
    public function __construct(KernelInterface $kernel, ContainerInterface $container)
    {
        $this->kernel = $kernel;
        $this->container = $container;
    }
    /**
     * Creates a Terrific Module
     *
     * @param \Terrific\ComposerBundle\Entity\Module $module the module to create
     */
    public function createModule(Module $module)
    {
        $src = __DIR__.'/../Template/Module/';
        $dst = $this->kernel->getRootDir().'/../src/Terrific/Module/'.ucfirst($module->getName()).'Bundle';

        $this->copy($src, $dst, $module);
    }


    /**
     * Gets all existing Modules
     *
     * @return array all exiting \Terrific\ComposerBundle\Entity\Module instances
     */
    public function getModules()
    {
        $modules = array();

        $dir = $this->kernel->getRootDir().'/../src/Terrific/Module/';

        $finder = new Finder();
        $finder->directories()->in($dir)->depth('== 0');

        foreach ($finder as $file) {
            $filename = $file->getFilename();
            $module = str_replace('Bundle', '', $filename);
            $modules[$module] = $this->getModuleByName($module, 'small');
        }

        return $modules;
    }

    /**
     * Gets the appropriate Module for a given module name.
     *
     * @param string name the module name
     * @param string format the format to retrieve (small | full)
     * @return \Terrific\ComposerBundle\Entity\Module the appropriate module
     */
    public function getModuleByName($name = null, $format = 'full') {

        $templates = array();

        if (isset($name)) {
            $dir = $this->kernel->getRootDir().'/../src/Terrific/Module/'.$name.'Bundle';

            $module = new Module();
            $module->setName($name);


            if($format == 'full') {
                // get templates
                $finder = new Finder();
                $finder->files()->in($dir.'/Resources/views/')->name('*.twig');

                foreach ($finder as $file) {
                    // setup a fresh template object
                    $template = new ModuleTemplate();

                    // fill it
                    $path = str_replace(str_replace('/app', '', $this->kernel->getRootDir()), '',  $file->getRealPath());
                    $path = str_replace('.html.twig', '', $path);
                    $path = str_replace('/src/Terrific/Module/'.$name.'Bundle/Resources/views/', '', $path);

                    $template->setName($path);

                    try {
                        // check whether the path is controller action or a simple view
                        $paths = explode('/', $path);
                        $controller = $paths[0].'Controller';
                        $action = $paths[1].'Action';

                        $c = new \ReflectionClass('\Terrific\Module\\'.$name.'Bundle\Controller\\'.$controller);

                        $c->getMethod($action);
                        $template->setPath($paths[0].':'.$paths[1]);
                    }
                    catch(\Exception $e) {
                        // it is a simple view
                        $template->setPath($path);
                    }

                    $module->addTemplate($template);
                }


                // get skins
                if (is_dir($dir.'/Resources/public/css/skin')) {
                    $finder = new Finder();
                    $finder->files()->in($dir.'/Resources/public/css/skin')->name('*.less')->name('*.css');

                    foreach ($finder as $file) {
                        // setup a fresh skin object
                        $skin = new Skin();

                        // fill it
                        $skin->setName(str_replace('.less', '', str_replace('.css', '', $file->getFilename())));
                        $module->addSkin($skin);
                    }
                }

                if (is_dir($dir.'/Resources/public/js/skin')) {
                    $finder = new Finder();
                    $finder->files()->in($dir.'/Resources/public/js/skin')->name('*.js');

                    foreach ($finder as $file) {
                        // setup a fresh skin object
                        $skin = new Skin();

                        // fill it
                        $skin->setName(str_replace('.js', '', $file->getFilename()));
                        $module->addSkin($skin);
                    }
                }
            }

            return $module;
        }
        else {
            throw new \Exception('Please specify a Module name');
        }
    }


    /**
     * Copy the default module.
     *
     * @param String $src the source path
     * @param String $dst the destination path
     * @param \Terrific\ComposerBundle\Entity\Module $module the module
     * @return void
     */
    protected function copy($src, $dst, Module $module)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file) && ($file != 'skin' || $file == 'skin' && $module->getSkins())) {
                    $this->copy($src . '/' . $file, $dst . '/' . $file, $module);
                }
                else if (!is_dir($src . '/' . $file)) {
                    $old = $src . '/' . $file;
                    $new = '';

                    switch ($file) {
                        case 'TerrificModuleDefaultBundle.php':
                            $new = $dst . '/TerrificModule' . ucfirst($module->getName()) . 'Bundle.php';
                            if(!empty($new) && !file_exists($new)) {
                                copy($old, $new);
                                $this->rewrite($new,
                                    array('Your Name', 'Default', 'default', 'skinName'),
                                    array($module->getAuthor(), ucfirst($module->getName()), strtolower($module->getName()), ''));
                            }
                            break;

                        case 'module.' . $module->getStyle():
                            $new = $dst . '/' . strtolower($module->getName()) . '.' . $module->getStyle();
                            if(!empty($new) && !file_exists($new)) {
                                copy($old, $new);
                                $this->rewrite($new,
                                    array('Your Name', 'Default', 'default', 'skinName'),
                                    array($module->getAuthor(), ucfirst($module->getName()), strtolower($module->getName()), ''));
                            }
                            break;

                        case 'skin.less':
                            foreach($module->getSkins() as $skin) {
                                if($skin->getStyle() == 'less') {
                                    $new = $dst . '/' . strtolower($skin->getName()) . '.' . $skin->getStyle();
                                    if(!empty($new) && !file_exists($new)) {
                                        copy($old, $new);
                                        $this->rewrite($new,
                                            array('Your Name', 'Default', 'default', 'skinName'),
                                            array($skin->getAuthor(), ucfirst($skin->getModule()), strtolower($skin->getModule()), ucfirst($skin->getName())));
                                    }
                                }
                            }
                            break;


                        case 'skin.css':
                            foreach($module->getSkins() as $skin) {
                                if($skin->getStyle() == 'css') {
                                    $new = $dst . '/' . strtolower($skin->getName()) . '.' . $skin->getStyle();
                                    if(!empty($new) && !file_exists($new)) {
                                        copy($old, $new);
                                        $this->rewrite($new,
                                            array('Your Name', 'Default', 'default', 'skinName'),
                                            array($skin->getAuthor(), ucfirst($skin->getModule()), strtolower($skin->getModule()), ucfirst($skin->getName())));
                                    }
                                }
                            }
                            break;

                        case 'default.html.twig':
                            $new = $dst . '/' . strtolower($module->getName()) . '.html.twig';
                            if(!empty($new) && !file_exists($new)) {
                                copy($old, $new);
                                $this->rewrite($new,
                                    array('Your Name', 'Default', 'default', 'skinName'),
                                    array($module->getAuthor(), ucfirst($module->getName()), strtolower($module->getName()), ''));
                            }
                            break;

                        case 'Tc.Module.Default.js':
                            $new = $dst . '/' . 'Tc.Module.' . ucfirst($module->getName()) . '.js';
                            if(!empty($new) && !file_exists($new)) {
                                copy($old, $new);
                                $this->rewrite($new,
                                    array('Your Name', 'Default', 'default', 'skinName'),
                                    array($module->getAuthor(), ucfirst($module->getName()), strtolower($module->getName()), ''));
                            }
                            break;

                        case 'Tc.Module.Default.Skin.js':
                            foreach($module->getSkins() as $skin) {
                                $new = $dst . '/' . 'Tc.Module.' . ucfirst($skin->getModule()) . '.' . ucfirst($skin->getName()) . '.js';
                                if(!empty($new) && !file_exists($new)) {
                                    copy($old, $new);
                                    $this->rewrite($new,
                                        array('Your Name', 'Default', 'default', 'skinName'),
                                        array($skin->getAuthor(), ucfirst($skin->getModule()), strtolower($skin->getModule()), ucfirst($skin->getName())));
                                }
                            }
                            break;

                        case 'default.md':
                            $new = $dst . '/' . strtolower($module->getName()) . '.md';
                            if(!empty($new) && !file_exists($new)) {
                                copy($old, $new);
                                $this->rewrite($new,
                                    array('Your Name', 'Default', 'default', 'skinName'),
                                    array($module->getAuthor(), ucfirst($module->getName()), strtolower($module->getName()), ''));
                            }
                            break;

                        default:
                            // do nothing
                            break;
                    }
                }
            }
        }
        closedir($dir);
    }


    protected function rewrite($new, $search, $replace)
    {
        $file = @file_get_contents($new);
        if ($file) {
            $file = str_replace($search, $replace, $file);
            file_put_contents($new, $file);
        }
    }
}