<?php

namespace Ecg\MagentoFinder\FileInfo;

use Ecg\MagentoFinder\FileInfo,
    SebastianBergmann\PHPLOC\Analyser;

class PhpFileInfo extends FileInfo implements PhpFileInfoInterface, PhpClassInfoInterface
{
    /**
     * @return array
     */
    public function getCodeMetrics()
    {
        $analyzer = new Analyser();
        return $analyzer->countFiles(array($this->getRelativePath()), null);
    }

    /**
     *
     */
    public function getDispatchedEvents()
    {
        // TODO: Implement getDispatchedEvents() method.
    }

    function getClassName()
    {
        // TODO: Implement getClassName() method.
    }

    function getInterfaceNames()
    {
        // TODO: Implement getInterfaceNames() method.
    }
}
