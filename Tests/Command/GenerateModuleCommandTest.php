<?php

/*
 * This file is part of the Terrific Composer Bundle.
 *
 * (c) Remo Brunschwiler <remo@terrifically.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terrific\ComposerBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;
use Terrific\ComposerBundle\Command\GenerateModuleCommand;
use Terrific\ComposerBundle\Tests\Mock\KernelForTest;

class GenerateModuleCommandTest extends \PHPUnit_Framework_TestCase
{
    private $command;

    public function setUp()
    {
        $application = new Application();
        $application->add(new GenerateModuleCommand());
        $this->command = $application->find('terrific:generate:module');

    }

    public function testEmpty()
    {
        $commandTester = new CommandTester($this->command);

        try {
            $commandTester->execute(array('command' => $this->command->getName()));
        } catch (\RuntimeException $expected) {
            return;
        }

        $this->fail('An expected runtime exception has not been raised.');
    }
}
