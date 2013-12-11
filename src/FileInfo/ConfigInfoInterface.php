<?php

namespace Ecg\MagentoFinder\FileInfo;

interface ConfigInfoInterface extends FileInfoInterface
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
     * @return array
     */
    function getRouters();

}
