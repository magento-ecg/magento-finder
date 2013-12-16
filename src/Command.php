<?php

namespace Ecg\MagentoFinder;

use Ecg\MagentoFinder\FileInfo\ModuleInfo,
    Symfony\Component\Console\Command\Command as SymfonyCommand,
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
            ->addOption('rewrites', null, InputOption::VALUE_NONE, 'Get rewrites')
            ->addOption('event-listeners', null, InputOption::VALUE_NONE, 'Get event listeners')
            ->addOption('cron-jobs', null, InputOption::VALUE_NONE, 'Get cron jobs')
            ->addOption('layout-updates', null, InputOption::VALUE_NONE, 'Get layout updates files')
            ->addOption('overrides', null, InputOption::VALUE_NONE, 'Get parent classes');
    }

    /**
     * @todo validate path
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

        if ($helper->getCurrentDepth($path) == Helper::MODULE_DEPTH) {
            $mageParts = $helper->getMagePathParts($path);
            $module = new ModuleInfo($path, '', '', array('path_parts' => $mageParts));
            $finder->append(array($module));
            $depth = 0;
        }

        $finder->modules()->in($path)->depth($depth);

        if($input->getOption('list-modules')) {
            foreach ($finder as $module) {
                echo $module->getName();
                echo ' (v' . $module->getVersion() . ')';
                echo PHP_EOL;
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

        if($input->getOption('event-listeners')) {
            foreach ($finder as $module) {
                echo $module->getName() . PHP_EOL;
                print_r($module->getEventListeners());
                echo PHP_EOL . PHP_EOL;
            }
            return;
        }

        if($input->getOption('cron-jobs')) {
            foreach ($finder as $module) {
                echo $module->getName() . PHP_EOL;
                print_r($module->getCronJobs());
                echo PHP_EOL . PHP_EOL;
            }
            return;
        }

        if($input->getOption('layout-updates')) {
            foreach ($finder as $module) {
                echo $module->getName() . PHP_EOL;
                print_r($module->getLayoutUpdates());
                echo PHP_EOL . PHP_EOL;
            }
            return;
        }

        if($input->getOption('overrides')) {
            foreach ($finder as $module) {
                echo $module->getName() . PHP_EOL;
                print_r($module->getOverrides());
                echo PHP_EOL . PHP_EOL;
            }
            return;
        }
    }
}
