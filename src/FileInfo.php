<?php

namespace Ecg\MagentoFinder;

use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

class FileInfo extends SymfonySplFileInfo
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
}
