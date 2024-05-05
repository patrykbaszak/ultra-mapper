<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Presentation\CLI;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/** @codeCoverageIgnore until this command will be ready */
#[AsCommand(
    name: 'ultramapper:blueprint:generate',
    description: 'Generates a blueprint from a given source class.'
)]
class GenerateBlueprint extends Command
{
    protected function configure(): void
    {
        $this->addArgument('class', InputArgument::REQUIRED, 'The class to generate a blueprint from.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // todo
        return Command::SUCCESS;
    }
}
