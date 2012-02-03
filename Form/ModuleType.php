<?php

/*
 * This file is part of the Terrific Composer package.
 *
 * (c) Remo Brunschwiler <remo@terrifically.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terrific\ComposerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Module form type.
 */
class ModuleType extends AbstractType
{
    /**
     * Builds the module form.
     *
     * @param \Symfony\Component\Form\FormBuilder $builder
     * @param array $options
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name', 'text');
        $builder->add('style', 'choice', array(
            'choices' => array('css' => 'CSS', 'less' => 'LESS'),
            'multiple' => false,
            'expanded' => true
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'module';
    }
}

