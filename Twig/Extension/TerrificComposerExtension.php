<?php

/*
 * This file is part of the Terrific Composer Bundle.
 *
 * (c) Remo Brunschwiler <remo@terrifically.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terrific\ComposerBundle\Twig\Extension;

use Symfony\Component\HttpKernel\KernelInterface;
use Twig_Test_Method;
use Twig_Filter_Method;
use Symfony\Component\Finder\Finder;

class TerrificComposerExtension extends \Twig_Extension
{
    private $compositionBundles;

    /**
     * Constructor.
     *
     * @param Array $compositionBundles An array of composition bundle paths
     */
    public function __construct($compositionBundles)
    {
        $this->compositionBundles = $compositionBundles;
    }

    /**
     * {@inheritdoc}
     */
    function initRuntime(\Twig_Environment $environment)
    {
        // extend the loader paths
        $currentLoader = $environment->getLoader();

        foreach($this->compositionBundles as $compositionBundle) {
            $dir =  $compositionBundle . '/Resources/macros/';
            $currentLoader->setPaths(array_merge($currentLoader->getPaths(), array($dir)));

            // load the composition macros
            if(file_exists($dir)) {
                $finder = new Finder();
                $finder->files()->in($dir)->depth('== 0');

                foreach ($finder as $file) {
                   $filename = $file->getFilename();

                   if(strpos($filename, 'html') !== false) {
                        $parts = explode('.', $file->getFilename());
                        $macro = $parts[0];
                        $environment->addGlobal($macro, $environment->loadTemplate($file->getFilename()));
                   }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    function getName()
    {
        return 'terrific_composer';
    }
}



