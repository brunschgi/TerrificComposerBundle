<?php

namespace Terrific\ComposerBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Terrific\ComposerBundle\DependencyInjection\TerrificComposerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Filesystem\Filesystem;
use Terrific\ComposerBundle\Entity\Module;
use Terrific\ComposerBundle\Entity\Skin;

class ModuleManagerTest extends WebTestCase
{
    private $moduleManager;

    public function setUp()
    {
        $container = $this->createCompiledContainerForConfig(array());
        $this->moduleManager = $container->get('terrific.composer.module.manager');
    }

    public function tearDown()
    {
        // kill Test module
        $filesystem = new Filesystem();
        $filesystem->remove(__DIR__ . '/Fixtures/src/Terrific/Module/Test');
    }

    public function testGetModules()
    {
        $modules = $this->moduleManager->getModules();

        $this->assertEquals(3, count($modules));
        $this->assertEquals('Hero', $modules[count($modules) - 3]->getName());
        $this->assertEquals('Intro', $modules[count($modules) - 2]->getName());
        $this->assertEquals('Teaser', $modules[count($modules) - 1]->getName());

    }

    public function testGetModuleByNameEmpty()
    {
        try {
            $module = $this->moduleManager->getModuleByName();
        } catch (\Exception $expected) {
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }

    public function testGetModuleByNameWithoutJs()
    {
        $module = $this->moduleManager->getModuleByName('Teaser');

        $this->assertEquals(1, count($module->getTemplates()));
        $this->assertEquals(0, count($module->getSkins()));
        $this->assertEquals('Teaser', $module->getName());
    }

    public function testGetModuleByNameWithoutSkinsAndOneTemplate()
    {
        $module = $this->moduleManager->getModuleByName('Intro');

        $this->assertEquals(1, count($module->getTemplates()));
        $this->assertEquals(0, count($module->getSkins()));
        $this->assertEquals('Intro', $module->getName());
    }

    public function testGetModuleByNameWithSkinsAndTemplates()
    {
        $module = $this->moduleManager->getModuleByName('Hero');

        $this->assertEquals(3, count($module->getTemplates()));
        $this->assertEquals(1, count($module->getSkins()));
        $this->assertEquals('Hero', $module->getName());
    }

    public function testCreateSimpleLessModule()
    {
        $module = new Module();
        $module->setName('Test');
        $module->setStyle('less');
        $module->addTemplate("test");

        $this->moduleManager->createModule($module);

        $module = $this->moduleManager->getModuleByName('Test');

        $templates = $module->getTemplates();
        $this->assertEquals(1, count($templates));
        $this->assertEquals('test', $templates[0]->getName());
        $this->assertEquals(0, count($module->getSkins()));
        $this->assertEquals('Test', $module->getName());
    }

    public function testCreateLessModuleWithSkinsAndTemplates()
    {
        $module = new Module();
        $module->setName('Test');
        $module->setStyle('less');
        $module->addTemplate("test1");
        $module->addTemplate("test2");

        $skin = new Skin();
        $skin->setName('More');
        $skin->setStyle('less');
        $module->addSkin($skin);

        $skin = new Skin();
        $skin->setName('SpecialMore');
        $skin->setStyle('less');
        $module->addSkin($skin);

        $this->moduleManager->createModule($module);

        $module = $this->moduleManager->getModuleByName('Test');

        $templates = $module->getTemplates();
        $this->assertEquals(2, count($templates));
        $this->assertEquals('test1', $templates[0]->getName());
        $this->assertEquals('test2', $templates[1]->getName());

        $skins = $module->getSkins();
        $this->assertEquals(2, count($skins));
        $this->assertEquals('More', $skins[0]->getName());
        $this->assertEquals('less', $skins[0]->getStyle());
        $this->assertEquals('SpecialMore', $skins[1]->getName());
        $this->assertEquals('less', $skins[1]->getStyle());

        $this->assertEquals('Test', $module->getName());
    }

    public function testCreateLessModuleWithStrangeInputs()
    {
        $module = new Module();
        $module->setName('test');
        $module->setStyle('less');
        $module->addTemplate("TEST1");
        $module->addTemplate("tEsT2");

        $skin = new Skin();
        $skin->setName('More');
        $skin->setStyle('less');
        $module->addSkin($skin);

        $skin = new Skin();
        $skin->setName('special-more');
        $skin->setStyle('less');
        $module->addSkin($skin);

        $this->moduleManager->createModule($module);

        $module = $this->moduleManager->getModuleByName('Test');

        $templates = $module->getTemplates();
        $this->assertEquals(2, count($templates));
        $this->assertEquals('test1', $templates[0]->getName());
        $this->assertEquals('test2', $templates[1]->getName());

        $skins = $module->getSkins();
        $this->assertEquals(2, count($skins));
        $this->assertEquals('More', $skins[0]->getName());
        $this->assertEquals('less', $skins[0]->getStyle());
        $this->assertEquals('SpecialMore', $skins[1]->getName());
        $this->assertEquals('less', $skins[1]->getStyle());

        $this->assertEquals('Test', $module->getName());
    }

    public function testEmptyModuleTemplate() {
        $container = $this->createCompiledContainerForConfig(array('module_template' => ''));
        $this->moduleManager = $container->get('terrific.composer.module.manager');

        $module = new Module();
        $module->setName('Test');
        $module->setStyle('less');
        $module->addTemplate("test");

        $this->moduleManager->createModule($module);

        // check that README.md is empty
        $content = file_get_contents(__DIR__ . '/Fixtures/src/Terrific/Module/Test/README.md');
        $this->assertEquals('', $content);
    }

    public function testModuleTemplateWithReadme() {
        $container = $this->createCompiledContainerForConfig(array('module_template' => __DIR__ . '/Fixtures/src/Terrific/Composition/Template/ModuleReadme'));
        $this->moduleManager = $container->get('terrific.composer.module.manager');

        $module = new Module();
        $module->setName('Test');
        $module->setStyle('less');
        $module->addTemplate("test");

        $this->moduleManager->createModule($module);

        // check that README.md has some default content
        $content = file_get_contents(__DIR__ . '/Fixtures/src/Terrific/Module/Test/README.md');
        $this->assertEquals('#Module Template', $content);
    }

    public function testModuleTemplateWithAdditionalResources() {
        $container = $this->createCompiledContainerForConfig(array('module_template' => __DIR__ . '/Fixtures/src/Terrific/Composition/Template/ModuleAdditional'));
        $this->moduleManager = $container->get('terrific.composer.module.manager');

        $module = new Module();
        $module->setName('Test');
        $module->setStyle('less');
        $module->addTemplate("test");

        $this->moduleManager->createModule($module);

        // check that the additional resources have been created
        $content = file_get_contents(__DIR__ . '/Fixtures/src/Terrific/Module/Test/Resources/public/css/my.less');
        $this->assertEquals('/* My Less */', $content);

        $content = file_get_contents(__DIR__ . '/Fixtures/src/Terrific/Module/Test/Resources/public/js/my.js');
        $this->assertEquals('/* My Js */', $content);

        $content = file_get_contents(__DIR__ . '/Fixtures/src/Terrific/Module/Test/Resources/views/my.html.twig');
        $this->assertEquals('{# My Twig #}', $content);
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
            'kernel.root_dir' => __DIR__ . '/Fixtures/src',
            'kernel.charset' => 'UTF-8',
            'kernel.debug' => false,
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

