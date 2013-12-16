<?php

namespace Ecg\MagentoFinder\FileInfo;

interface PhpClassInfoInterface
{
    /**
     * @return string
     */
    function getClassName();

    /**
     * @return string
     */
    function getParentClassName();

    /**
     * @return array
     */
    function getInterfaceNames();
}
