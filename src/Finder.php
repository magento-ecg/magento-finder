<?php

namespace Ecg\MagentoFinder;

use Ecg\MagentoFinder\Adapter\PhpAdapter,
    Symfony\Component\Finder\Finder as SimfonyFinder;

class Finder extends SimfonyFinder
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->removeAdapters()
            ->addAdapter(new PhpAdapter(), -50)
            ->setAdapter('magento-php');
    }

    /**
     * Restricts the matching to modules only.
     *
     * @return Finder The current Finder instance
     *
     * @api
     */
    public function modules()
    {
        foreach ($this->getAdapters() as $adapter) {
            $adapter->setOnlyModules(true);
        }
        return $this;
    }
}
