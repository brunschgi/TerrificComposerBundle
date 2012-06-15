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
use Terrific\ComposerBundle\Entity\Page;
use Terrific\ComposerBundle\Entity\Template as ModuleTemplate;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * PageManager.
 */
class PageManager
{
    private $kernel;
    private $router;
    private $compositionBundles;

    /**
     * Constructor.
     *
     * @param KernelInterface $kernel The kernel is used to parse bundle notation
     * @param RouterInterface $router The router is used to generate paths
     * @param Array $compositionBundles An array of composition bundle paths
     */
    public function __construct(KernelInterface $kernel, RouterInterface $router, $compositionBundles)
    {
        $this->kernel = $kernel;
        $this->router = $router;
        $this->compositionBundles = $compositionBundles;
    }

    /**
     * Gets all existing Pages
     *
     * @return array All existing Page instances
     */
    public function getPages()
    {
        $pages = array();
        $reader = new AnnotationReader();

        foreach($this->compositionBundles as $compositionBundle) {
            $dir = $this->kernel->locateResource($compositionBundle, null, true) . 'Controller/';

            $finder = new Finder();
            $finder->files()->in($dir)->depth('== 0')->name('*Controller.php');

            foreach ($finder as $file) {
                $className = str_replace('.php', '', $file->getFilename());
                $path = str_replace(str_replace('app', '', $this->kernel->getRootDir()), '', $file->getPathname());
                $path = str_replace('src', '', $path);
                $path = str_replace('/', '\\', $path);
                $path = str_replace('.php', '', $path);
                $c = new \ReflectionClass($path);

                $methods = $c->getMethods();

                foreach($methods as $method) {
                    // check whether the method is an action and therefore a page
                    if (strpos($method->getName(), 'Action') !== false) {
                        // setup a fresh page object
                        $page = new Page();
                        $page->setController(substr($className, 0, -10));
                        $action = substr($method->getShortName(), 0, -6);
                        $page->setAction($action);

                        // create name from composer annotation
                        $composerAnnotation = $reader->getMethodAnnotation($method, 'Terrific\ComposerBundle\Annotation\Composer');
                        if($composerAnnotation == null) {
                            $name = $action;
                        }
                        else {
                            $name = $composerAnnotation->getName();
                        }

                        $page->setName($name);

                        // create url from route annotation
                        $routeAnnotation = $reader->getMethodAnnotation($method, 'Symfony\Component\Routing\Annotation\Route');

                        $page->setUrl($this->router->generate($routeAnnotation->getName()));

                        $pages[] = $page;
                    }
                }
            }
        }

        return $pages;
    }
}