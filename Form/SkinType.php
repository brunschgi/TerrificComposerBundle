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
use Symfony\Component\Finder\Finder;

/**
 * Skin form type.
 */
class SkinType extends AbstractType
{
    /**
     * Builds the Skin form.
     *
     * @param \Symfony\Component\Form\FormBuilder $builder
     * @param array $options
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        global $kernel;

        // get all modules
        $modules = array();

        $dir = $kernel->getRootDir().'/../src/Terrific/Module/';

        $finder = new Finder();
        $finder->directories()->in($dir)->depth('== 0');

        foreach ($finder as $file) {
            $filename = $file->getFilename();
            $module = str_replace('Bundle', '', $filename);
            $modules[$module] = $module;
        }

        $builder->add('module', 'choice', array(
            'choices' => $modules,
            'multiple' => false,
            'label' => 'Skin for Module'
        ));
        $builder->add('name', 'text');
        $builder->add('style', 'choice', array(
            'choices' => array('css' => 'CSS', 'less' => 'LESS'),
            'multiple' => false,
            'expanded' => true
        ));
        $builder->add('author', 'text');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'skin';
    }
}

