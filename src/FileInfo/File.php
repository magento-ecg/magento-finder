<?php

namespace Ecg\MagentoFinder\FileInfo;

use Ecg\MagentoFinder\FileInfo,
    Ecg\MagentoFinder\Finder,
    SebastianBergmann\PHPLOC\Analyser;

class File extends FileInfo implements FileInterface
{
    /**
     * @var array
     */
    public $info = array();

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
        parent::__construct($file, $relativePath, $relativePathname);
        $this->info = $info;
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

    /**
     *
     */
    public function getDispatchedEvents()
    {
        // TODO: Implement getDispatchedEvents() method.
    }
}
