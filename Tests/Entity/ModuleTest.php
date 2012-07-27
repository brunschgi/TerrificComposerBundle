<?php

/*
 * This file is part of the Terrific Composer Bundle.
 *
 * (c) Remo Brunschwiler <remo@terrifically.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terrific\ComposerBundle\Tests\Entity;

use Terrific\ComposerBundle\Entity\Module;
use Terrific\ComposerBundle\Entity\Skin;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    public function testSearchResultType()
    {
        $module = new Module();

        $this->assertEquals('module', $module->getType());
    }

    public function testAddSkin()
    {
        $module = new Module();

        // add default skin (lower case first letter)
        $skin = new Skin();
        $skin->setName('default');
        $module->addSkin($skin);
        $stack = $module->getSkins();
        $this->assertEquals('Default', $stack[count($stack)-1]->getName());
        $this->assertEquals(1, count($stack));

        // add Default skin (upper case first letter)
        $skin = new Skin();
        $skin->setName('Default');
        $module->addSkin($skin);
        $stack = $module->getSkins();
        $this->assertEquals('Default', $stack[count($stack)-1]->getName());
        $this->assertEquals(1, count($stack));

        // add more skin (lower case first letter)
        $skin = new Skin();
        $skin->setName('more');
        $module->addSkin($skin);
        $stack = $module->getSkins();
        $this->assertEquals('More', $stack[count($stack)-1]->getName());
        $this->assertEquals(2, count($stack));

        // add More skin (upper case first letter)
        $skin = new Skin();
        $skin->setName('More');
        $module->addSkin($skin);
        $stack = $module->getSkins();
        $this->assertEquals('More', $stack[count($stack)-1]->getName());
        $this->assertEquals(2, count($stack));

        // test order by adding First skin
        $skin = new Skin();
        $skin->setName('First');
        $module->addSkin($skin);
        $stack = $module->getSkins();
        $this->assertEquals('First', $stack[count($stack)-2]->getName());
        $this->assertEquals(3, count($stack));

        // test camel case skin
        $skin = new Skin();
        $skin->setName('moreSpecial');
        $module->addSkin($skin);
        $stack = $module->getSkins();
        $this->assertEquals('MoreSpecial', $stack[count($stack)-1]->getName());
        $this->assertEquals(4, count($stack));

        // test dashed skin
        $skin = new Skin();
        $skin->setName('more-special');
        $module->addSkin($skin);
        $stack = $module->getSkins();
        $this->assertEquals('MoreSpecial', $stack[count($stack)-1]->getName());
        $this->assertEquals(4, count($stack));

    }
}
