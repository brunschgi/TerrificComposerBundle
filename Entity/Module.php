<?php

/*
 * This file is part of the Terrific Composer package.
 *
 * (c) Remo Brunschwiler <remo@terrifically.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terrific\ComposerBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Module Entity.
 */
class Module
{
    /**
     * @var string $author
     */
    private $author;

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
     *
     * @var array skins
     */
    protected $skins;

    /**
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @param array $skins
     */
    public function setSkins($skins)
    {
        $this->skins = $skins;
    }

    /**
     * @return array
     */
    public function getSkins()
    {
        return $this->skins;
    }
}