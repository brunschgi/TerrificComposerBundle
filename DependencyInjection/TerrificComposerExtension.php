<?php

/*
 * This file is part of the Terrific Composer Bundle.
 *
 * (c) Remo Brunschwiler <remo@terrifically.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terrific\ComposerBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Terrific\ComposerBundle\EventListener\ToolbarListener;

/**
 * TerrificComposerExtension.
 *
 * Usage:
 *
 *     <terrific_composer:config
 *        toolbar="true"
 *    />
 *
 * @author Remo Brunschwiler <remo@terrifically.org>
 */
class TerrificComposerExtension extends Extension
{
    /**
     * Loads the terrific composer configuration.
     *
     * @param array            $configs   An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        // Toolbar
        $loader->load('toolbar.xml');
        if ($config['toolbar'] === 'demo') {
            $mode = ToolbarListener::DEMO;
        }
        else if ($config['toolbar']) {
            $mode = ToolbarListener::ENABLED;
        }
        else {
            $mode = ToolbarListener::DISABLED;
        }

        $container->setParameter('terrific_composer.toolbar.mode', $mode);

        // Other Services
        $loader->load('services.xml');

    }
}
