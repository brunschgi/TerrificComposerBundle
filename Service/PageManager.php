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
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * PageManager.
 */
class PageManager
{
    const COMPOSER_ANNOTATION_CLASS = 'Terrific\\ComposerBundle\\Annotation\\Composer';
    const ROUTE_ANNOTATION_CLASS = 'Symfony\\Component\\Routing\\Annotation\Route';

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    private $reader;

    public function __construct(ContainerInterface $container, RouterInterface $router, Reader $reader)
    {
        $this->container = $container;
        $this->router = $router;
        $this->reader = $reader;
    }

    /**
     * Gets all existing Pages
     *
     * @return array All existing Page instances
     */
    public function getPages()
    {
        $pages = array();

        foreach ($this->router->getRouteCollection()->all() as $route) {
            if ($method = $this->getReflectionMethod($route->getDefault('_controller'))) {
                if ($composerAnnotation = $this->reader->getMethodAnnotation($method, self::COMPOSER_ANNOTATION_CLASS)) {
                    if ($composerAnnotation !== null) {
                        // setup a fresh page object
                        $page = new Page();
                        $page->setName($composerAnnotation->getName());

                        // create url from route annotation or from route pattern
                        $routeAnnotation = $this->reader->getMethodAnnotation($method, self::ROUTE_ANNOTATION_CLASS);

                        try {
                            $page->setUrl($this->router->generate($routeAnnotation->getName()));
                        }
                        catch (RouteNotFoundException $e) {
                            $this->container->get('logger')->info('The @Route annotation of '.$route->getDefault('_controller').' has no name. Please specify it for better page linking.');
                            $page->setUrl($this->router->getContext()->getBaseUrl().$route->getPattern());
                        }

                        // add page
                        $pages[] = $page;
                    }
                }
            }
        }

        return $pages;
    }

    /**
     * Returns the ReflectionMethod for the given controller string
     *
     * @param string $controller
     * @return ReflectionMethod|null
     */
    private function getReflectionMethod($controller)
    {
        if (preg_match('#(.+)::([\w]+)#', $controller, $matches)) {
            $class = $matches[1];
            $method = $matches[2];
        } elseif (preg_match('#(.+):([\w]+)#', $controller, $matches)) {
            $controller = $matches[1];
            $method = $matches[2];
            if ($this->container->has($controller)) {
                $this->container->enterScope('request');
                $this->container->set('request', new Request);
                $class = get_class($this->container->get($controller));
                $this->container->leaveScope('request');
            }
        }

        if (isset($class) && isset($method)) {
            try {
                return new \ReflectionMethod($class, $method);
            } catch (\ReflectionException $e) {
            }
        }

        return null;
    }

}