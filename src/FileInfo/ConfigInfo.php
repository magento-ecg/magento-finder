<?php

namespace Ecg\MagentoFinder\FileInfo;

use Ecg\MagentoFinder\FileInfo,
    SimpleXMLElement;

class ConfigInfo extends FileInfo implements ConfigInfoInterface
{
    public function getRewrites()
    {
        $res = array();
        $xml = new SimpleXMLElement($this->getContents());
        $result = $xml->xpath('//rewrite');
        foreach ($result as $item) {
            foreach ($item as $c => $v) {
                $parent = $item->xpath("parent::*");
                if (empty($parent)) {
                    continue;
                }
                $b = $parent[0]->getName();
                $grandParent = $parent[0]->xpath("parent::*");
                if (empty($grandParent)) {
                    continue;
                }

                $a = $grandParent[0]->getName();
                $res[] = $v . ',' . $a . '_' . $b . '_' . $c . PHP_EOL;

            }
        }

        return $res;
    }

    public function getEventListeners()
    {
        // TODO: Implement getEventListeners() method.
    }

    public function getCronJobs()
    {
        // TODO: Implement getCronJobs() method.
    }

    /**
     * @return array
     */
    public function getVersion()
    {
        $xml = new SimpleXMLElement($this->getContents());
        return (string)$xml->xpath('//modules/'. $this->info['name'] . '/version')[0];
    }

    /**
     * @return array
     */
    function getRouters()
    {
        // TODO: Implement getRouters() method.
    }
}
