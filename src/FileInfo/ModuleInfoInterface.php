<?php

namespace Ecg\MagentoFinder\FileInfo;

use Ecg\MagentoFinder\Finder;

interface ModuleInfoInterface extends PhpFileInfoInterface
{
    /**
     * @return string
     */
    function getName();

    /**
     * @return mixed
     */
    function getCodepool();

    /**
     * @return mixed
     */
    function getNamespace();

    /**
     * @return ConfigInfo
     */
    function getConfig();

    /**
     * @param string $pattern
     * @return Finder
     */
    function getFiles($pattern = '/.*/');

    /**
     * @return array
     */
    function getOverrides();

    /**
     * @return array
     */
    function getDuplicatedCode();
}
