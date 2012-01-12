<?php

namespace Terrific\Composer\AppBundle\Form;

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

