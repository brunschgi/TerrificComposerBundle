<?php

/*
 * This file is part of the Terrific Composer Bundle.
 *
 * (c) Remo Brunschwiler <remo@terrifically.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terrific\ComposerBundle\Tests\DependencyInjection;

use Terrific\ComposerBundle\DependencyInjection\TerrificComposerExtension;
use Terrific\ComposerBundle\EventListener\ToolbarListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class TerrificComposerExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyConfiguration()
    {
        $config = array();
        $container = $this->createCompiledContainerForConfig($config);

        $this->assertInstanceOf('Terrific\ComposerBundle\Service\ModuleManager', $container->get('terrific.composer.module.manager'));
        $this->assertEquals(ToolbarListener::DISABLED, $container->getParameter('terrific_composer.toolbar.mode'));
        $this->assertEquals('TerrificComposition::base.html.twig', $container->getParameter('terrific_composer.module.layout'));

        $bundles = $container->getParameter('terrific_composer.composition.bundles');
        $this->assertEquals(1, count($bundles));
        $this->assertEquals('@TerrificComposition', $bundles[count($bundles)-1]);
    }

    public function testToolbarModeEnabled()
    {
        $config = array('toolbar' => true);
        $container = $this->createCompiledContainerForConfig($config);

        $this->assertEquals(ToolbarListener::ENABLED, $container->getParameter('terrific_composer.toolbar.mode'));
    }

    public function testToolbarModeDisabled()
    {
        $config = array('toolbar' => false);
        $container = $this->createCompiledContainerForConfig($config);

        $this->assertEquals(ToolbarListener::DISABLED, $container->getParameter('terrific_composer.toolbar.mode'));
    }

    public function testToolbarModeDemo()
    {
        $config = array('toolbar' => 'demo');
        $container = $this->createCompiledContainerForConfig($config);

        $this->assertEquals(ToolbarListener::DEMO, $container->getParameter('terrific_composer.toolbar.mode'));
    }

    public function testCustomModuleLayout()
    {
        $config = array('module_layout' => '@TerrificComposition::custom.html.twig');
        $container = $this->createCompiledContainerForConfig($config);

        $this->assertEquals('TerrificComposition::custom.html.twig', $container->getParameter('terrific_composer.module.layout'));
    }

    public function testCustomCompositionBundles()
    {
        $config = array('composition_bundles' => array('@TerrificComposition', 'TerrificComposerBundle', '@TestBundle'));
        $container = $this->createCompiledContainerForConfig($config);

        $bundles = $container->getParameter('terrific_composer.composition.bundles');
        $this->assertEquals(3, count($bundles));
        $this->assertEquals('@TerrificComposition', $bundles[count($bundles)-3]);
        $this->assertEquals('@TerrificComposerBundle', $bundles[count($bundles)-2]);
        $this->assertEquals('@TestBundle', $bundles[count($bundles)-1]);
    }

    private function createCompiledContainerForConfig($config)
    {
        $container = $this->createContainer();
        $container->registerExtension(new TerrificComposerExtension());
        $container->loadFromExtension('terrific_composer', $config);
        $this->compileContainer($container);

        return $container;
    }

    private function createContainer()
    {
        $container = new ContainerBuilder(new ParameterBag(array(
            'kernel.root_dir' => __DIR__,
            'kernel.charset'   => 'UTF-8',
            'kernel.debug'     => false,
        )));

        return $container;
    }

    private function compileContainer(ContainerBuilder $container)
    {
        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();
    }
}