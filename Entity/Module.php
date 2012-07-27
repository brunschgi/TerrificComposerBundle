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
 * Module Entity.
 */
class Module implements SearchResult
{
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
    protected $skins = array();

    /**
     *
     * @var array templates
     */
    protected $templates = array();

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
        usort($this->skins, function($a, $b)
        {
            if ($a->getName() == $b->getName())
            {
                return 0;
            }
            else if ($a->getName() > $b->getName())
            {
                return 1;
            }
            else {
                return -1;
            }
        });

        return $this->skins;
    }

    /**
     * @param Skin $skin
     */
    public function addSkin($skin) {
        $exists = false;

        foreach($this->skins as $existingSkin) {
            if($skin->getName() == $existingSkin->getName()) {
                $exists = true;
            }
        }

        if(!$exists) {
            $skin->setModule($this->getName());
            array_push($this->skins, $skin);
        }
    }

    /**
     * @param array $templates
     */
    public function setTemplates($templates)
    {
        $this->templates = $templates;
    }

    /**
     * @return array
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * @param Template $template
     */
    public function addTemplate($template) {
        array_push($this->templates, $template);
    }

    /**
     * @param string $templateName
     */
    public function getTemplateByName($templateName = null) {
        if(!$templateName) {
            // get first template
            $template = current($this->templates);
        }
        else {
            $templateName = str_replace(':','/',$templateName);

            // get the appropriate template
            foreach($this->templates as $tmpTemplate) {
                if($tmpTemplate->getName() == $templateName) {
                    $template = $tmpTemplate;
                }
            }
        }

        return $template;
    }

    public function getType()
    {
        return "module";
    }
}