<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Blueprint\Presentation\CLI;

use PBaszak\UltraMapper\Blueprint\Domain\Accessor\Accessor;
use PBaszak\UltraMapper\Blueprint\Domain\Entity\Blueprint;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Yaml\Yaml;

/** @codeCoverageIgnore until this command will be ready */
#[AsCommand(
    name: 'ultramapper:blueprint:generate',
    description: 'Generates a blueprint from a given source class.'
)]
class GenerateBlueprint extends Command
{
    private string $blueprintDir;

    public function __construct(
        #[Autowire(param: 'kernel.project_dir')]
        string $projectDir,
        string $blueprintDir = '/var/ultra-mapper/blueprints/',
    ) {
        parent::__construct();

        $this->blueprintDir = $projectDir.$blueprintDir;
        if (!is_dir($this->blueprintDir)) {
            mkdir($this->blueprintDir, 0777, true);
        }
    }

    protected function configure(): void
    {
        $this->addArgument('class', InputArgument::REQUIRED, 'The class to generate a blueprint from.');
        $this->addOption('save', 's', null, 'Save the blueprint to the file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $class = $input->getArgument('class');

        $blueprint = Blueprint::create($class, null);
        $aggregate = (new Accessor($blueprint))->getBlueprintAggregate();

        $data = $aggregate?->normalize() ?? [$blueprint->blueprintName => $blueprint->normalize()];

        if ($input->getOption('save')) {
            $filename = $this->blueprintDir.$blueprint->blueprintName.'.yaml';
            file_put_contents($filename, Yaml::dump($data, 10));
            $output->writeln('Blueprint saved to '.$filename);
        } else {
            $output->writeln(Yaml::dump($data, 10));
        }

        return Command::SUCCESS;
    }
}
