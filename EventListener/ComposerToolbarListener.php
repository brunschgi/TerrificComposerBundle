<?php

/*
 * This file is part of the Terrific Composer Bundle.
 *
 * (c) Remo Brunschwiler <remo@terrifically.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terrific\ComposerBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;

/**
 * ComposerToolbarListener injects the Terrific Composer Toolbar.
 *
 * The onKernelResponse method must be connected to the kernel.response event.
 *
 * The Terrific Composer Toolbar is only injected on well-formed HTML (with a proper </body> tag).
 * This means that the WDT is never included in sub-requests or ESI requests.
 *
 * @author Remo Brunschwiler <remo@terrifically.org>
 */
class ComposerToolbarListener
{
    const DISABLED        = 1;
    const ENABLED         = 2;
    const DEMO            = 3;

    protected $templating;
    protected $mode;
    private $container;

    /**
     * Constructor.
     *
     * @param TwigEngine $templating The templating engine
     * @param int $mode The mode of the toolbar
     * @param ContainerInterface    $container    The container is used to load the managers lazily, thus avoiding a circular dependency
     */
    public function __construct(TwigEngine $templating, ContainerInterface $container, $mode = self::ENABLED)
    {
        $this->templating = $templating;
        $this->container = $container;
        $this->mode = (integer) $mode;
    }

    public function isEnabled()
    {
        return self::ENABLED === $this->mode;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $params = array();

        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();

        // do not capture redirects or modify XML HTTP Requests
        if ($request->isXmlHttpRequest()) {
            return;
        }

        // disable the toolbar if necessary
        if (self::DISABLED === $this->mode) {
            return;
        }

        // set configurator only in module details view
        $params['configurator'] = false;
        if($request->attributes->get('_route') == 'composer_module_details') {
            $params['configurator'] = true;
        }
        if($params['configurator']) {
            $moduleManager = $this->container->get('terrific.composer.module.manager');
            $params['module'] = $moduleManager->getModuleByName($request->attributes->get('module'));

            // prepare the parameter for module rendering
            $template = $params['module']->getTemplateByName($request->attributes->get('template'));
            $params['template'] = $template->getPath();

            $skins = $request->attributes->get('skins');
            $skins = explode(',', $skins);
            $params['skins'] = $skins;
        }

        $this->injectToolbar($response, $params);
    }

    /**
     * Injects the Terrific Composer Toolbar into the given Response.
     *
     * @param Response $response A Response instance
     * @param array $params The parameters
     */
    protected function injectToolbar(Response $response, $params)
    {
        if (function_exists('mb_stripos')) {
            $posrFunction = 'mb_strripos';
            $substrFunction = 'mb_substr';
        } else {
            $posrFunction = 'strripos';
            $substrFunction = 'substr';
        }

        $content = $response->getContent();

        if (false !== $pos = $posrFunction($content, '</body>')) {
            $toolbar = "\n".str_replace("\n", '', $this->templating->render(
                'TerrificComposerBundle:Toolbar:toolbar.html.twig', $params
            ))."\n";
            $content = $substrFunction($content, 0, $pos).$toolbar.$substrFunction($content, $pos);
            $response->setContent($content);
        }
    }
}
