<?php

namespace Ecg\MagentoFinder\FileInfo;

use Ecg\MagentoFinder\FileInfo,
    Ecg\MagentoFinder\Finder,
    SimpleXMLElement,
    Symfony\Component\Finder\SplFileInfo;

class Module extends FileInfo
{
    public function getRewrites()
    {
        $finder = new Finder();
        $finder->name('config.xml')->in($this->getRealPath());

        $res = array();

        /**@var $file SplFileInfo * */
        foreach ($finder as $file) {
            $xml = new SimpleXMLElement($file->getRealPath(), 0, true);
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
        }
        return $res;
    }
}
