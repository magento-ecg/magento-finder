<?php

namespace Ecg\MagentoFinder\FileInfo;

use Ecg\MagentoFinder\FileInfo,
    Ecg\MagentoFinder\Finder;
use SebastianBergmann\PHPLOC\Analyser;

class ModuleInfo extends FileInfo implements ModuleInfoInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var ConfigInfo
     */
    protected $config;

    /**
     * @return ConfigInfo
     */
    public function getConfig()
    {
        if (!$this->config) {
            $configPathName = DIRECTORY_SEPARATOR . 'etc';
            $configPath = $configPathName . DIRECTORY_SEPARATOR . 'config.xml';
            $this->config = new ConfigInfo($this->getRealPath() . $configPath, $this->getRelativePath() . $configPath, $this->getRelativePathname() . $configPathName,
                array('name' => $this->getName()));
        }
        return $this->config;
    }

    public function getRewrites()
    {
        $this->getConfig()->getRewrites();
    }

    public function getEventListeners()
    {
        return $this->getConfig()->getEventListeners();
    }

    public function getCronJobs()
    {
        return $this->getConfig()->getСronJobs();
    }

    /**
     * @return array
     */
    public function getVersion()
    {
        return $this->getConfig()->getVersion();
    }

    /**
     * @todo ugly
     * @return string
     */
    public function getName()
    {
        if (!$this->name) {
            $module = end($this->info['path_parts']);
            $codepool = prev($this->info['path_parts']);
            $this->name = $codepool . '_' . $module;
        }

        return $this->name;
    }

    /**
     * @param string $pattern
     * @return FileInfo
     */
    public function getFiles($pattern = '/.*/')
    {
        $finder = new Finder();
        return $finder->files()->in($this->getRealPath())->name($pattern);
    }

    /**
     * @return array
     */
    function getOverrides()
    {
        // TODO: Implement getOverrides() method.
    }

    /**
     * @return mixed
     */
    function getCodepool()
    {
        // TODO: Implement getCodepool() method.
    }

    /**
     * @return mixed
     */
    function getNamespace()
    {
        // TODO: Implement getNamespace() method.
    }

    /**
     * @return array
     */
    public function getCodeMetrics()
    {
        $analyzer = new Analyser();
        $finder = new Finder();
        $finder->name('*.php')->in($this->getRealPath());

        return $analyzer->countFiles(iterator_to_array($finder), null);
    }

    function getDispatchedEvents()
    {
        // TODO: Implement getDispatchedEvents() method.
    }
}
