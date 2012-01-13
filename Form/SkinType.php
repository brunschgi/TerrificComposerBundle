<?php

namespace Terrific\ComposerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Skin form type.
 */
class SkinType extends AbstractType
{
    /**
     * Builds the skin form.
     *
     * @param \Symfony\Component\Form\FormBuilder $builder
     * @param array $options
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('author', 'text');
        $builder->add('namespace', 'text');
        $builder->add('name', 'text');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'module';
    }
}

