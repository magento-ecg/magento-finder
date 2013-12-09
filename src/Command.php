<?php

namespace Ecg\MagentoFinder;

use Symfony\Component\Console\Command\Command as SymfonyCommand,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

class Command extends SymfonyCommand
{
    protected function configure()
    {
        $this->setName('magento-finder')
            ->setDescription('Magento Finder Tool')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to code');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder();
        $finder->modules()->in($input->getArgument('path'));
        foreach ($finder as $module) {
            print_r($module->getRewrites());
        }
    }
}
