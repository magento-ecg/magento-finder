<?php

namespace Ecg\MagentoFinder\FileInfo;

use Ecg\MagentoFinder\FileInfo,
    Ecg\MagentoFinder\Helper,
    SimpleXMLElement;

class ConfigInfo extends FileInfo implements ConfigInfoInterface
{
    /**
     * @var SimpleXmlElement
     */
    protected $xml;

    /**
     * Constructor
     *
     * @param string $file The file name
     * @param string $relativePath The relative path
     * @param string $relativePathname The relative path name
     * @param array $info
     */
    public function __construct($file, $relativePath, $relativePathname, $info)
    {
        parent::__construct($file, $relativePath, $relativePathname, $info);
        $this->xml = new SimpleXMLElement($this->getContents());
        $this->helper = new Helper;
    }

    public function getRewrites()
    {
        $res = array();

        foreach ($this->xml->xpath('//rewrite') as $item) {
            foreach ($item as $c => $v) {
                $parent = $item->xpath('..');
                if (empty($parent)) {
                    continue;
                }
                $b = $parent[0]->getName();
                $grandParent = $parent[0]->xpath('..');
                if (empty($grandParent)) {
                    continue;
                }

                $a = $grandParent[0]->getName();
                if ($a == 'config') {
                    continue; // skip url rewrites
                }
//                $res[] = array(
//                    'from' => $a . '_' . $b . '_' . $c,
//                    'to'   => (string)$v
//                );
                $className = $this->getGroupedClassName($a, $b, $c);
                $res[$className] [] = (string) $v;

            }
        }

        return $res;
    }

    public function getGroupedClassName($groupType, $classId, $suffix)
    {
        $namespace = (strpos($classId, '_') !== false) ? '' : 'mage_';
        $groupType = substr($groupType, 0, strlen($groupType) - 1);
        $className = $namespace . $classId . '_' . $groupType . '_' . $suffix;
        return $this->helper->ucWords($className);
    }

        /**
     * @todo deal with disabled observers
     * @return array
     */
    public function getEventListeners()
    {
        $res = array();
        $events = $this->xml->xpath('//events/*');

        foreach ($events as $event) {
            $area = $event->xpath('../..');
            $class = $event->xpath('.//class');
            $method = $event->xpath('.//method');
            if ($area && $class && $method) {
                $res[] = array(
                    'area' => (string)$area[0]->getName(),
                    'event' => $event->getName(),
                    'class' => (string)$class[0],
                    'run_method' => (string)$method[0]
                );
            }
        }

        return $res;
    }

    public function getCronJobs()
    {
        $res = array();
        $cronJobs = $this->xml->xpath('//crontab/jobs/*');

        foreach ($cronJobs as $job) {
            $res[] = array(
                'schedule' => (string)$job->schedule->cron_expr,
                'run_method' => (string)$job->run->model,
            );
        }
        return $res;
    }

    /**
     * @return array
     */
    public function getVersion()
    {
        $version = $this->xml->xpath('//modules/' . $this->info['name'] . '/version');
        return count($version) ? (string)$version[0] : '';
    }

    /**
     * @return array
     */
    public function getRouters()
    {
        // TODO: Implement getRouters() method.
    }

    public function getLayoutUpdates()
    {
        $updates = $this->xml->xpath('//layout/updates/*');

        $res = array();
        foreach ($updates as $update) {
            $res[$update->getName()] = array(
                'file' => (string)$update->file,
                'area' => (string)$update->xpath('../../..')[0]->getName()
            );
        }
        return $res;
    }
}
