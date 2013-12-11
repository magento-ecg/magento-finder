<?php

namespace Ecg\MagentoFinder\Iterator;

use Ecg\MagentoFinder\FileInfo,
    Ecg\MagentoFinder\FileInfo\ModuleInfo,
    Ecg\MagentoFinder\Helper,
    Symfony\Component\Finder\Iterator\FileTypeFilterIterator as SymfonyFileTypeFilterIterator,
    Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

class FileTypeFilterIterator extends SymfonyFileTypeFilterIterator
{
    const ONLY_MODULES = 3;

    /**
     * @var int
     */
    protected $mode;

    /**
     * @var \Ecg\MagentoFinder\Helper
     */
    protected $helper;

    /**
     * @param \Iterator $iterator
     * @param int $mode
     */
    public function __construct(\Iterator $iterator, $mode)
    {
        $this->mode = $mode;
        $this->helper = new Helper;
        parent::__construct($iterator, $mode);
    }

    /**
     * @return FileInfo
     */
    public function current()
    {
        $path = parent::current()->getRealPath();
        $mageParts = $this->helper->getMagePathParts($path);
        $depth = count($mageParts);
        $info = array('path_parts' => $mageParts);

        if ($mageParts === false || !in_array('code', $mageParts))
            return new FileInfo(parent::current()->getPathname(), $this->getSubPath(), $this->getSubPathname(), $info);

        switch ($depth) {
            case Helper::MODULE_DEPTH :
                if (!is_dir($path))
                    return new FileInfo(parent::current()->getPathname(), $this->getSubPath(), $this->getSubPathname(), $info);
                return new ModuleInfo(parent::current()->getPathname(), $this->getSubPath(), $this->getSubPathname(), $info);
            case Helper::COMPONENT_DEPTH :
                return new FileInfo(parent::current()->getPathname(), $this->getSubPath(), $this->getSubPathname(), $info);
            default :
                return new FileInfo(parent::current()->getPathname(), $this->getSubPath(), $this->getSubPathname(), $info);
        }
    }

    /**
     * Filters the iterator values.
     *
     * @return Boolean true if the value should be kept, false otherwise
     */
    public function accept()
    {
        $fileInfo = $this->current();
        if (self::ONLY_MODULES === (self::ONLY_MODULES & $this->mode) && ($fileInfo instanceof ModuleInfo)) {
            return true;
        }
        return parent::accept();
    }
}
