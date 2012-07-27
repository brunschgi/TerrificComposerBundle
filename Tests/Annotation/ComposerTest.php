<?php

/*
 * This file is part of the Terrific Composer Bundle.
 *
 * (c) Remo Brunschwiler <remo@terrifically.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terrific\ComposerBundle\Tests\Annotation;

use Terrific\ComposerBundle\Annotation\Composer;

class ComposerTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyAnnotation()
    {
        $annotation = new Composer(array());

        $this->assertEquals('', $annotation->getName());
    }

    public function testSimpleAnnotation()
    {
        $annotation = new Composer(array("value" => 'Simple Annotation'));

        $this->assertEquals('Simple Annotation', $annotation->getName());
    }

    public function testNamedParamAnnotation()
    {
        $annotation = new Composer(array("name" => 'Named Param Annotation'));

        $this->assertEquals('Named Param Annotation', $annotation->getName());
    }


}
