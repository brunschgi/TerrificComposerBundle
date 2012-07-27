<?php

/*
 * This file is part of the Terrific Composer Bundle.
 *
 * (c) Remo Brunschwiler <remo@terrifically.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terrific\ComposerBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Terrific\ComposerBundle\Util\StringUtils;

/**
 * Skin Entity.
 */
class Skin
{
    /**
     * @var string $module
     */
    private $module;

    /**
     * @var string $style
     */
    private $style;

    /**
     *
     * @Assert\NotBlank()
     * @var string $name
     */
    protected $name;

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = StringUtils::camelize($name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $style
     */
    public function setStyle($style)
    {
        $this->style = $style;
    }

    /**
     * @return string
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @param string $module
     */
    public function setModule($module)
    {
        $this->module = StringUtils::camelize($module);
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }


}