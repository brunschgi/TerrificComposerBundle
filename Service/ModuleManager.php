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

/**
 * ModuleManager.
 */
class ModuleManager
{
    public function createModule(Module $module)
    {
        $src = __DIR__.'/../Template/Module/';
        $dst = __DIR__.'/../../../../../src/Terrific/Module/'.$module->getName().'Bundle';

        $this->copy($src, $dst, $module);
    }

    /**
     * Copy the default module.
     *
     * @param String $src the source path
     * @param String $dst the destination path
     * @param Module $module the module
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
                            $new = $dst . '/TerrificModule' . $module->getName() . 'Bundle.php';
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

                        case 'skin.' . $module->getStyle():
                            foreach($module->getSkins() as $skin) {
                                $new = $dst . '/' . strtolower($skin) . '.' . $module->getStyle();
                                if(!empty($new) && !file_exists($new)) {
                                    copy($old, $new);
                                    $this->rewrite($new,
                                        array('Your Name', 'Default', 'default', 'skinName'),
                                        array($module->getAuthor(), ucfirst($module->getName()), strtolower($module->getName()), $skin));
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
                            $new = $dst . '/' . 'Tc.Module.' . $module->getName() . '.js';
                            if(!empty($new) && !file_exists($new)) {
                                copy($old, $new);
                                $this->rewrite($new,
                                    array('Your Name', 'Default', 'default', 'skinName'),
                                    array($module->getAuthor(), ucfirst($module->getName()), strtolower($module->getName()), ''));
                            }
                            break;

                        case 'Tc.Module.Default.Skin.js':
                            foreach($module->getSkins() as $skin) {
                                $new = $dst . '/' . 'Tc.Module.' . $module->getName() . '.' . $skin . '.js';
                                if(!empty($new) && !file_exists($new)) {
                                    copy($old, $new);
                                    $this->rewrite($new,
                                        array('Your Name', 'Default', 'default', 'skinName'),
                                        array($module->getAuthor(), ucfirst($module->getName()), strtolower($module->getName()), $skin));
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