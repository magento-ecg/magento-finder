<?php

namespace Ecg\MagentoFinder\FileInfo;

use Ecg\MagentoFinder\FileInfo,
    SebastianBergmann\PHPLOC\Analyser;

class PhpFileInfo extends FileInfo implements PhpFileInfoInterface
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
}
