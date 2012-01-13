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

/**
 * ModuleManager.
 */
class ModuleManager
{
    public function createModule($options)
    {
        $src = __DIR__.'/../Template/Module/';
        $dst = __DIR__.'/../../../../../src/Terrific/Module/'.$options['module'].'Bundle';

        $skin = false;
        if(!empty($options['skin'])) {
            $skin = $options['skin'];
        }

        $this->copy($src, $dst, $options['module'], $options['author'], $skin, $options['style']);
    }

    /**
     * Copy the default module.
     *
     * @param String $src the source path
     * @param String $dst the destination path
     * @param String $module the module name
     * @param String $author the author
     * @param String $skin the name of the skin
     * @param String $style the name of the style
     * @return void
     */
    protected function copy($src, $dst, $module, $author, $skin, $style)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file) && ($file != 'skin' || $file == 'skin' && $skin)) {
                    $this->copy($src . '/' . $file, $dst . '/' . $file, $module, $author, $skin, $style);
                }
                else if (!is_dir($src . '/' . $file)) {
                    $old = $src . '/' . $file;
                    $new = '';

                    switch ($file) {
                        case 'TerrificModuleDefaultBundle.php':
                            $new = $dst . '/TerrificModule' . $module . 'Bundle.php';
                            break;

                        case 'module.' . $style:
                            $new = $dst . '/' . strtolower($module) . '.' . $style;
                            break;

                        case 'skin.' . $style:
                            $new = $dst . '/' . strtolower($skin) . '.' . $style;
                            break;

                        case 'default.html.twig':
                            $new = $dst . '/' . strtolower($module) . '.html.twig';
                            break;

                        case 'Tc.Module.Default.js':
                            $new = $dst . '/' . 'Tc.Module.' . $module . '.js';
                            break;

                        case 'Tc.Module.Default.Skin.js':
                            $new = $dst . '/' . 'Tc.Module.' . $module . '.' . $skin . '.js';
                            break;

                        case 'default.md':
                            $new = $dst . '/' . strtolower($module) . '.md';
                            break;

                        default:
                            // do nothing
                            break;
                    }

                    if (!empty($new) && !file_exists($new)) {
                        copy($old, $new);

                        $this->rewrite($new,
                            array('Your Name', 'Default', 'default', 'skinName'),
                            array($author, ucfirst($module), strtolower($module), ($skin ? $skin : 'skinName'))
                        );
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