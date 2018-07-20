<?php

namespace ShoppingCartBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ShoppingCartBundle\DataFixtures\ORM\ShoppingCartDataFixtures;

class SeedCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('db:seed')

            // the short description shown while running "php bin/console list"
            ->setDescription('Seed Fake Database!')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to seed database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(["Seeding..."]);

        $SeedCommand = new ShoppingCartDataFixtures();
        $SeedCommand->getFixtures();
    }
}
