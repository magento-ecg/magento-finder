<?php

namespace Ecg\MagentoFinder\Adapter;

use Ecg\MagentoFinder\Iterator\FileTypeFilterIterator,
    Symfony\Component\Finder\Adapter\PhpAdapter as SymfonyPhpAdapter,
    Symfony\Component\Finder\Iterator;

class PhpAdapter extends SymfonyPhpAdapter
{
    /**
     * @var bool
     */
    protected $onlyModules = false;

    /**
     * @return string
     */
    public function getName()
    {
        return 'magento-php';
    }

    /**
     * @param $value
     */
    public function setOnlyModules($value)
    {
        $this->onlyModules = $value;
    }

    public function searchInDirectory($dir)
    {
        $iterator = parent::searchInDirectory($dir);
        if ($this->onlyModules) {
            $this->setMode(FileTypeFilterIterator::ONLY_MODULES);
            $iterator = new FileTypeFilterIterator($iterator, $this->mode);
        }
        return $iterator;
    }
}
