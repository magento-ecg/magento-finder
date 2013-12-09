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
     * @param string $pattern
     * @return FileInfo
     */
    public function getFiles($pattern = '/.*/')
    {
        $finder = new Finder();
        return $finder->files()->in($this->getRealPath())->name($pattern);
    }
}
