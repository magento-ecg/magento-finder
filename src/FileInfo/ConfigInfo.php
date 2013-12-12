<?php

namespace Ecg\MagentoFinder\FileInfo;

use Ecg\MagentoFinder\FileInfo,
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
    }

    public function getRewrites()
    {
        $res = array();

        $result = $this->xml->xpath('//rewrite');
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
        $res = array();
        $events = $this->xml->xpath('//events/*');

        foreach ($events as $event) {
            $res[] = array(
                'area' => (string)$event->xpath('../..')[0]->getName(),
                'event' => $event->getName(),
                'class' => (string)$event->xpath('.//class')[0],
                'run_method' => (string)$event->xpath('.//method')[0]
            );
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
        return (string)$this->xml->xpath('//modules/' . $this->info['name'] . '/version')[0];
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
            $res[$update->getName()] = (string)$update->file;
        }
        return $res;
    }
}
