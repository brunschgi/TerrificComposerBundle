<?php

/*
 * This file is part of the Terrific Composer Bundle.
 *
 * (c) Remo Brunschwiler <remo@terrifically.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terrific\ComposerBundle\Tests\Util;

use Terrific\ComposerBundle\Util\StringUtils;

class StringUtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testCamelize()
    {
        $name = "camel";
        $this->assertEquals('Camel', StringUtils::camelize($name));

        $name = "Camel";
        $this->assertEquals('Camel', StringUtils::camelize($name));

        $name = "more-camel";
        $this->assertEquals('MoreCamel', StringUtils::camelize($name));

        $name = "moreCamel";
        $this->assertEquals('MoreCamel', StringUtils::camelize($name));

        $name = "MoreCamel";
        $this->assertEquals('MoreCamel', StringUtils::camelize($name));

        $name = "special-more-camel";
        $this->assertEquals('SpecialMoreCamel', StringUtils::camelize($name));
    }

    public function testDash()
    {
        $name = "dash";
        $this->assertEquals('dash', StringUtils::dash($name));

        $name = "Dash";
        $this->assertEquals('dash', StringUtils::dash($name));

        $name = "more-dash";
        $this->assertEquals('more-dash', StringUtils::dash($name));

        $name = "moreDash";
        $this->assertEquals('more-dash', StringUtils::dash($name));

        $name = "MoreDash";
        $this->assertEquals('more-dash', StringUtils::dash($name));

        $name = "SpecialMoreDash";
        $this->assertEquals('special-more-dash', StringUtils::dash($name));
    }
}
