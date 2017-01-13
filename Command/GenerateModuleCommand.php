<?php

/*
* This file is part of the Terrific Composer Bundle.
*
* (c) Remo Brunschwiler <remo@terrifically.org>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Terrific\ComposerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Terrific\ComposerBundle\Entity\Module;
use Terrific\ComposerBundle\Entity\Skin;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateModuleCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('terrific:generate:module')
            ->setDescription('Generates a terrific module')
            ->addArgument(
                'name',  InputArgument::REQUIRED, 'The name of the module'
            )
            ->addOption(
                'skin', '', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'A module skin', array()
            )
            ->addOption(
                'style', '', InputOption::VALUE_REQUIRED, 'The style format', 'less'
            )

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = trim($input->getArgument('name'));
        $skinNames = $input->getOption('skin');
        $style = $input->getOption('style');

        $moduleManager = $this->getContainer()->get('terrific.composer.module.manager');

        // setup a fresh module object
        $module = new Module();
        $module->setStyle($style);
        $module->setName($name);
        $module->addTemplate(strtolower($name));

        foreach($skinNames as $skinName) {
            $skin = new Skin();
            $skin->setName($skinName);
            $skin->setStyle($style);
            $skin->setModule($name);
            $module->addSkin($skin);
        }

        $moduleManager->createModule($module);

        $output->writeln(array(
            'The module "'.$name.'" has been created',
            '  * Style: '.$style
        ));

        if(!empty($skinNames)) {
            $output->writeln('  * Skins: '.implode(', ', $skinNames));
        }

        $output->writeln('');
    }
}
