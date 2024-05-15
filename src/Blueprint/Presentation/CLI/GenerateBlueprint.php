<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Presentation\CLI;

use PBaszak\UltraMapper\Blueprint\Application\Contract\BlueprintInterface;
use PBaszak\UltraMapper\Blueprint\Application\Model\Blueprint;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

#[AsCommand(
    name: 'ultramapper:blueprint:generate',
    description: 'Generates a blueprint from a given source class.'
)]
class GenerateBlueprint extends Command
{
    public function __construct(
        private BlueprintInterface $blueprint,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('class', InputArgument::REQUIRED, 'The class to generate a blueprint from.');
        $this->addOption('save', 's', null, 'Save the blueprint to the file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $class = $input->getArgument('class');

        $blueprint = Blueprint::create($class);

        $data = $blueprint->normalize();

        if ($input->getOption('save')) {
            $this->blueprint->saveBlueprint($blueprint);
            $output->writeln('Blueprint saved successfully. Filename: '.$blueprint->root.'.yaml.');
        } else {
            $output->writeln(Yaml::dump($data, 10));
        }

        return Command::SUCCESS;
    }
}
