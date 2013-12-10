<?php

namespace Ecg\MagentoFinder\FileInfo;

interface ModuleInterface extends FileInterface
{
    /**
     * @return array
     */
    function getRewrites();

    /**
     * @return array
     */
    function getEventListeners();

    /**
     * @return array
     */
    function getCronJobs();

    /**
     * @return array
     */
    function getVersion();

    /**
     * @return string
     */
    function getName();
}
