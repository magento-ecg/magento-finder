<?php

namespace Ecg\MagentoFinder\FileInfo;

use Ecg\MagentoFinder\FileInfo,
    Ecg\MagentoFinder\Finder,
    SebastianBergmann\FinderFacade\FinderFacade,
    SebastianBergmann\PHPCPD\Detector\Detector,
    SebastianBergmann\PHPCPD\Detector\Strategy\DefaultStrategy,
    SebastianBergmann\PHPLOC\Analyser;

class ModuleInfo extends FileInfo implements ModuleInfoInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $codepool;

    /**
     * @var ConfigInfo
     */
    protected $config;

    /**
     * Constructor
     *
     * @param string $file The file name
     * @param string $relativePath The relative path
     * @param string $relativePathname The relative path name
     * @param array $info
     */
    public function __construct($file, $relativePath, $relativePathname, $info)
    {
        parent::__construct($file, $relativePath, $relativePathname, $info);
        $module = end($this->info['path_parts']);
        $this->namespace = prev($this->info['path_parts']);
        $this->codepool = prev($this->info['path_parts']);
        $this->name = $this->namespace . '_' . $module;
    }

    /**
     * @return ConfigInfo
     */
    public function getConfig()
    {
        if ($this->config === null) {
            $configPathName = DIRECTORY_SEPARATOR . 'etc';
            $configPath = $configPathName . DIRECTORY_SEPARATOR . 'config.xml';
            $this->config = new ConfigInfo($this->getRealPath() . $configPath, $this->getRelativePath() . $configPath, $this->getRelativePathname() . $configPathName,
                array('name' => $this->getName()));
        }
        return $this->config;
    }

    public function getRewrites()
    {
        return $this->getConfig()->getRewrites();
    }

    public function getEventListeners()
    {
        return $this->getConfig()->getEventListeners();
    }

    public function getCronJobs()
    {
        return $this->getConfig()->getCronJobs();
    }

    /**
     * @return array
     */
    public function getVersion()
    {
        return $this->getConfig()->getVersion();
    }

    /**
     * @return array
     */
    public function getLayoutUpdates()
    {
        return $this->getConfig()->getLayoutUpdates();
    }

    /**
     * @return string
     */
    public function getName()
    {
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
    public function getOverrides()
    {
        $finder = new Finder();
        $finder->name('*.php')->in($this->getRealPath());

        $res = array();
        /** @var PhpFileInfo $file */
        foreach ($finder as $file) {
            if ($name = $file->getClassName()) {
                $res[] = array(
                    'class_name' => $name,
                    'parent_class_name' => $file->getParentClassName(),
                );
            }
        }
        return $res;
    }

    /**
     * @return mixed
     */
    public function getCodepool()
    {
        return $this->codepool;
    }

    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return $this->namespace;
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

    public function getDispatchedEvents()
    {
        // TODO: Implement getDispatchedEvents() method.
    }

    /**
     * @return array
     */
    public function getDuplicatedCode()
    {
        $finder = new FinderFacade(array($this->getRealPath()), array(), array('*.php'));
        $detector = new Detector(new DefaultStrategy);
        $clones = $detector->copyPasteDetection($finder->findFiles());

        $numClones = count($clones);

        $summary = array();

        if ($numClones > 0) {

            $files = array();
            $locations = array();
            $lines = 0;

            foreach ($clones as $clone) {
                foreach ($clone->getFiles() as $file) {
                    $filename = $file->getName();

                    if (!isset($files[$filename])) {
                        $files[$filename] = true;
                    }
                }

                $lines += $clone->getSize() * (count($clone->getFiles()) - 1);

                foreach ($clone->getFiles() as $file) {
                    $locations[] = sprintf(
                        "%s:%d-%d",
                        $file->getName(),
                        $file->getStartLine(),
                        $file->getStartLine() + $clone->getSize()
                    );
                }
            }

            $summary = array(
                'num_clones' => $numClones,
                'duplicated_lines' => $lines,
                'num_files' => count($files),
                'locations' => $locations
            );
        }

        $summary['percentage'] = $clones->getPercentage();
        $summary['total_loc'] = $clones->getNumLines();

        return $summary;
    }
}
