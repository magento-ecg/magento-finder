<?php

namespace Ecg\MagentoFinder\Iterator;

use Ecg\MagentoFinder\FileInfo,
    Ecg\MagentoFinder\FileInfo\Module,
    Symfony\Component\Finder\Iterator\FilterIterator,
    Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

class FileTypeFilterIterator extends FilterIterator
{
    const ONLY_FILES       = 1;
    const ONLY_DIRECTORIES = 2;
    const ONLY_MODULES     = 3;

    /**
     * @var int
     */
    protected $mode;

    /**
     * @param SymfonySplFileInfo $fileInfo
     * @return FileInfo
     */
    protected function getCurrent(SymfonySplFileInfo $fileInfo)
    {
        $info = array();

        $info['path'] = $fileInfo->getRealPath();
        $pathParts = explode(DIRECTORY_SEPARATOR, $fileInfo->getRealPath()); //@todo work on compatible DS
        $k = array_search('app', $pathParts);
        if (array_key_exists($k + 1, $pathParts) && $pathParts[$k + 1] == 'code' &&
            array_key_exists($k + 2, $pathParts) && in_array($pathParts[$k + 2], array('local', 'community', 'core'))) {
            $info['codepool'] = $pathParts[$k + 2];
            if (!array_key_exists($k + 3, $pathParts)) {
                return new FileInfo(parent::current()->getPathname(), $this->getSubPath(), $this->getSubPathname());
            } else {
                $info['namespace'] = $pathParts[$k + 3];
                if (!array_key_exists($k + 4, $pathParts)) {
                    return new FileInfo(parent::current()->getPathname(), $this->getSubPath(), $this->getSubPathname());
                } else {
                    $info['module'] = $info['namespace'] . '_' . $pathParts[$k + 4];
                    if (!array_key_exists($k + 5, $pathParts)) {
                        $file = new Module(parent::current()->getPathname(), $this->getSubPath(), $this->getSubPathname());
                        return $file;
                    } else {
                        $info['component'] = strtolower($pathParts[$k + 5]);
                        switch ($pathParts[$k + 5]) {
                            case 'Model' :
                                return new FileInfo(parent::current()->getPathname(), $this->getSubPath(), $this->getSubPathname());
                            case 'Helper' :
                                return new FileInfo(parent::current()->getPathname(), $this->getSubPath(), $this->getSubPathname());
                            case 'Controller' :
                                return new FileInfo(parent::current()->getPathname(), $this->getSubPath(), $this->getSubPathname());
                            case 'Block' :
                                return new FileInfo(parent::current()->getPathname(), $this->getSubPath(), $this->getSubPathname());
                            default  :
                                return new FileInfo(parent::current()->getPathname(), $this->getSubPath(), $this->getSubPathname());
                        }
                    }
                }
            }
        }

        return new FileInfo(parent::current()->getPathname(), $this->getSubPath(), $this->getSubPathname());
    }

    /**
     * @return FileInfo
     */
    public function current()
    {
        return $this->getCurrent(parent::current());
    }

    /**
     * Constructor.
     *
     * @param \Iterator $iterator The Iterator to filter
     * @param integer $mode     The mode (self::ONLY_FILES or self::ONLY_DIRECTORIES or self::ONLY_MODULES)
     */
    public function __construct(\Iterator $iterator, $mode)
    {
        $this->mode = $mode;
        parent::__construct($iterator);
    }

    /**
     * Filters the iterator values.
     *
     * @return Boolean true if the value should be kept, false otherwise
     */
    public function accept()
    {
        $fileInfo = $this->current();
        if (self::ONLY_MODULES === (self::ONLY_MODULES & $this->mode) && ($fileInfo instanceof Module)) {
            return true;
        } elseif (self::ONLY_DIRECTORIES === (self::ONLY_DIRECTORIES & $this->mode) && $fileInfo->isFile()) {
            return false;
        } elseif (self::ONLY_FILES === (self::ONLY_FILES & $this->mode) && $fileInfo->isDir()) {
            return false;
        }
        return true;
    }
}
