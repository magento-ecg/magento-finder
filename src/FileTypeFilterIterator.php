<?php

namespace Ecg\MagentoFinder\Iterator;

use Ecg\MagentoFinder\FileInfo,
    Symfony\Component\Finder\Iterator\FilterIterator;

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
     * @return FileInfo
     */
    public function current()
    {
        return new FileInfo(parent::current()->getPathname(), $this->getSubPath(), $this->getSubPathname());
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
        $fileinfo = $this->current();
        if (self::ONLY_MODULES === (self::ONLY_MODULES & $this->mode) && $fileinfo->isModule()) {
            return true;
        } elseif (self::ONLY_DIRECTORIES === (self::ONLY_DIRECTORIES & $this->mode) && $fileinfo->isFile()) {
            return false;
        } elseif (self::ONLY_FILES === (self::ONLY_FILES & $this->mode) && $fileinfo->isDir()) {
            return false;
        }
        return true;
    }
}
