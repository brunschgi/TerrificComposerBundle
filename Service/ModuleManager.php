<?php
/*
 * This file is part of the Terrific Composer Bundle.
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
use Terrific\ComposerBundle\Entity\Skin;
use Terrific\ComposerBundle\Entity\Template as ModuleTemplate;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Terrific\ComposerBundle\EventListener\ToolbarListener;
use Terrific\ComposerBundle\Util\StringUtils;

/**
 * ModuleManager.
 */
class ModuleManager
{
    /**
     * @var String The root directory
     */
    private $rootDir;

    /**
     * @var String The mode of the toolbar (true, false, 'demo')
     */
    private $toolbarMode;

    /**
     * @var String The layout to render in the module details view
     */
    private $moduleLayout;

    public function __construct($rootDir, $toolbarMode, $moduleLayout, $moduleTemplate)
    {
        $this->rootDir = $rootDir;
        $this->toolbarMode = $toolbarMode;
        $this->moduleLayout = $moduleLayout;
        $this->defaultTemplate = __DIR__.'/../Template/Module/';
        $this->moduleTemplate = $moduleTemplate;
    }

    /**
     * Creates a Terrific Module
     *
     * @param Module $module The module to create
     */
    public function createModule(Module $module)
    {
        $dst = $this->rootDir.'/../src/Terrific/Module/'.StringUtils::camelize($module->getName());

        if($this->toolbarMode === ToolbarListener::DEMO) {
            // prevent module creation in demo mode
            throw new \Exception('This action is not supported in demo mode');
        } else {
            if($this->moduleTemplate) {
                $this->copy($this->moduleTemplate, $dst, $module);
            }

            $this->copy($this->defaultTemplate, $dst, $module);
        }
    }

    /**
     * Creates a Terrific Skin
     *
     * @param Skin $skin The skin to create
     */
    public function createSkin(Skin $skin)
    {
        $module = new Module();
        $module->setName($skin->getModule());
        $module->addSkin($skin);

        $dst = $this->rootDir.'/../src/Terrific/Module/'.StringUtils::camelize($module->getName());

        if($this->toolbarMode === ToolbarListener::DEMO) {
            // prevent module creation in demo mode
            throw new \Exception('This action is not supported in demo mode');
        } else {
            if($this->moduleTemplate) {
                $this->copy($this->moduleTemplate, $dst, $module);
            }

            $this->copy($this->defaultTemplate, $dst, $module);
        }
    }

    /**
     * Gets all existing Modules
     *
     * @return array All existing Module instances
     */
    public function getModules()
    {
        $modules = array();

        $dir = $this->rootDir.'/../src/Terrific/Module/';

        $finder = new Finder();
        $finder->directories()->in($dir)->depth('== 0');

        foreach ($finder as $file) {
            $module = $file->getFilename();
            $modules[$module] = $this->getModuleByName($module, 'small');
        }

        sort($modules);

        return $modules;
    }

    /**
     * Gets the appropriate Module for a given module name.
     *
     * @param string name The module name
     * @param string format The format to retrieve (small | full)
     * @return Module The appropriate module
     */
    public function getModuleByName($name = null, $format = 'full') {

        if (isset($name)) {
            $dir = $this->rootDir.'/../src/Terrific/Module/'.$name;

            // setup a fresh module object
            $module = new Module();

            // fill it with the basic infos
            $module->setName($name);

            // fill it with all infos
            if($format == 'full') {
                // get templates
                $finder = new Finder();
                $finder->files()->in($dir.'/Resources/views/')->name('*.twig')->sortByName();

                foreach ($finder as $file) {
                    // setup a fresh template object
                    $template = new ModuleTemplate();

                    // fill it
                    $path = str_replace(str_replace('\\', '/', $this->rootDir), '',  str_replace('\\', '/', $file->getPathname()));
                    $path = str_replace('.html.twig', '', $path);
                    $path = str_replace('/../src/Terrific/Module/'.$name.'/Resources/views/', '', $path);

                    $template->setName($path);

                    try {
                        // check whether the path is a controller action or a simple view
                        $paths = explode('/', $path);
                        $controller = $paths[0].'Controller';
                        $action = $paths[1].'Action';

                        $c = new \ReflectionClass('\Terrific\Module\\'.$name.'\Controller\\'.$controller);

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
                    $finder->files()->in($dir.'/Resources/public/css/skin')->name('*.less')->name('*.css')->sortByName();

                    foreach ($finder as $file) {
                        // setup a fresh skin object
                        $skin = new Skin();

                        // fill it
                        $skin->setModule($module->getName());
                        if(strpos($file->getFilename(), '.less')) {
                            $skin->setStyle('less');
                        }
                        else {
                            $skin->setStyle('css');
                        }

                        $skin->setName(str_replace('.less', '', str_replace('.css', '', $file->getFilename())));
                        $module->addSkin($skin);
                    }
                }

                if (is_dir($dir.'/Resources/public/js/skin')) {
                    $finder = new Finder();
                    $finder->files()->in($dir.'/Resources/public/js/skin')->name('*.js')->sortByName();

                    foreach ($finder as $file) {
                        // setup a fresh skin object
                        $skin = new Skin();

                        // fill it
                        $skin->setModule($module->getName());
                        $skin->setStyle('js');
                        $skin->setName(str_replace('Tc.Module.'.$module->getName().'.', '', str_replace('.js', '', $file->getFilename())));
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
     * Returns the module layout.
     *
     * @return String The module layout
     */
    public function getModuleLayout() {
        return $this->moduleLayout;
    }

    /**
     * Copy the default module.
     *
     * @param String $src The source path
     * @param String $dst The destination path
     * @param Module $module The module
     * @return void
     */
    protected function copy($src, $dst, Module $module)
    {
        $dir = opendir($src);
        $author = 'Terrific Composer';
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
                        case 'TerrificModuleDefault.php':
                            $new = $dst . '/TerrificModule' . StringUtils::camelize($module->getName()) . '.php';
                            if(!empty($new) && !file_exists($new)) {
                                copy($old, $new);
                                $this->rewrite($new,
                                    array('Your Name', 'Default', 'SkinName'),
                                    array($author, (StringUtils::camelize($module->getName())), ''));
                            }
                            break;

                        case 'module.less':
                            if($module->getStyle() == 'less') {
                                $new = $dst . '/' . StringUtils::dash($module->getName()) . '.' . $module->getStyle();
                                if(!empty($new) && !file_exists($new)) {
                                    copy($old, $new);
                                    $this->rewrite($new,
                                        array('Your Name', 'default', 'skinname'),
                                        array($author, StringUtils::dash($module->getName()), ''));
                                }
                            }
                            break;

                        case 'module.css':
                            if($module->getStyle() == 'css') {
                                $new = $dst . '/' . StringUtils::dash($module->getName()) . '.' . $module->getStyle();
                                if(!empty($new) && !file_exists($new)) {
                                    copy($old, $new);
                                    $this->rewrite($new,
                                        array('Your Name', 'default', 'skinname'),
                                        array($author, StringUtils::dash($module->getName()), ''));
                                }
                            }
                            break;

                        case 'skin.less':
                            foreach($module->getSkins() as $skin) {
                                if($skin->getStyle() == 'less') {
                                    $new = $dst . '/' . StringUtils::dash($skin->getName()) . '.' . $skin->getStyle();
                                    if(!empty($new) && !file_exists($new)) {
                                        copy($old, $new);
                                        $this->rewrite($new,
                                            array('Your Name', 'default', 'skinname'),
                                            array($author, StringUtils::dash($skin->getModule()), StringUtils::dash($skin->getName())));
                                    }
                                }
                            }
                            break;

                        case 'skin.css':
                            foreach($module->getSkins() as $skin) {
                                if($skin->getStyle() == 'css') {
                                    $new = $dst . '/' . StringUtils::dash($skin->getName()) . '.' . $skin->getStyle();
                                    if(!empty($new) && !file_exists($new)) {
                                        copy($old, $new);
                                        $this->rewrite($new,
                                            array('Your Name', 'default', 'skinname'),
                                            array($author, StringUtils::dash($skin->getModule()), StringUtils::dash($skin->getName())));
                                    }
                                }
                            }
                            break;

                        case 'default.html.twig':
                            foreach($module->getTemplates() as $template) {
                                $new = $dst . '/' . strtolower($template) . '.html.twig';
                                if(!empty($new) && !file_exists($new)) {
                                    copy($old, $new);
                                    $this->rewrite($new,
                                        array('Your Name', 'Default', 'SkinName'),
                                        array($author, StringUtils::camelize($module->getName()), ''));
                                }
                            }
                            break;

                        case 'Tc.Module.Default.js':
                            $new = $dst . '/' . 'Tc.Module.' . StringUtils::camelize($module->getName()) . '.js';
                            if(!empty($new) && !file_exists($new)) {
                                copy($old, $new);
                                $this->rewrite($new,
                                    array('Your Name', 'Default', 'SkinName'),
                                    array($author, StringUtils::camelize($module->getName()), ''));
                            }
                            break;

                        case 'Tc.Module.Default.Skin.js':
                            foreach($module->getSkins() as $skin) {
                                $new = $dst . '/' . 'Tc.Module.' . StringUtils::camelize($skin->getModule()) . '.' . StringUtils::camelize($skin->getName()) . '.js';
                                if(!empty($new) && !file_exists($new)) {
                                    copy($old, $new);
                                    $this->rewrite($new,
                                        array('Your Name', 'Default', 'SkinName'),
                                        array($author, StringUtils::camelize($skin->getModule()), StringUtils::camelize($skin->getName())));
                                }
                            }
                            break;

                        case 'README.md':
                            $new = $dst . '/README.md';
                            if(!empty($new) && !file_exists($new)) {
                                copy($old, $new);
                            }
                            break;

                        default:
                            // copy file
                            $new = $dst . '/' . $file;
                            if(!empty($new) && !file_exists($new)) {
                                copy($old, $new);
                            }
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