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
            ->addArgument('path', InputArgument::REQUIRED, 'Path to code')
            ->addOption('list-modules', null, InputOption::VALUE_NONE, 'Get list of modules')
            ->addOption('code-metrics', null, InputOption::VALUE_NONE, 'Get code metrics')
            ->addOption('rewrites', null, InputOption::VALUE_NONE, 'Get rewrites');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder;
        $helper = new Helper;
        $path   = $input->getArgument('path');
        $depth  = Helper::MODULE_DEPTH - $helper->getCurrentDepth($path) - 1;

        $finder->modules()->in($path)->depth($depth);

        if($input->getOption('list-modules')) {
            foreach ($finder as $module) {
                echo $module->getName() . PHP_EOL;
            }
            return;
        }

        if($input->getOption('code-metrics')) {
            foreach ($finder as $module) {
                echo $module->getName() . PHP_EOL;
                print_r($module->getCodeMetrics());
                echo PHP_EOL . PHP_EOL;
            }
            return;
        }

        if($input->getOption('rewrites')) {
            foreach ($finder as $module) {
                echo $module->getName() . PHP_EOL;
                print_r($module->getRewrites());
                echo PHP_EOL . PHP_EOL;
            }
            return;
        }
    }
}
