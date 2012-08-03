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

use Terrific\ComposerBundle\Entity\Module;
use Terrific\ComposerBundle\Entity\Skin;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;

class GenerateModulesCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('terrific:generate:modules')
            ->setDescription('Generates a bunch of terrific modules')
            ->addArgument(
                'names',  InputArgument::IS_ARRAY, 'The name of the modules'
            )
            ->addOption(
                'style', '', InputOption::VALUE_REQUIRED, 'The style format', 'less'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $names = $input->getArgument('names');
        $style = $input->getOption('style');
        $command = $this->getApplication()->find('terrific:generate:module');

        foreach($names as $name) {
            $arguments = array(
                'command' => 'terrific:generate:module',
                'name'    => $name,
                '--style'  => $style,
            );

            $input = new ArrayInput($arguments);
            $output->writeln($command->run($input, $output));
        }

        $output->writeln(array(
            '',
            '',
            'The following modules have been created (Style: '.$style.')'
        ));

        foreach($names as $name) {
            $output->writeln('  * '.$name);
        }
    }
}
