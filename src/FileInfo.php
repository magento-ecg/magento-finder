<?php

namespace Ecg\MagentoFinder;

use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

class FileInfo extends SymfonySplFileInfo
{
    const IS_CODEPOOL   = 23;
    const IS_NAMESPACE  = 24;
    const IS_MODULE     = 25;
    const IS_MODEL      = 26;
    const IS_HELPER     = 27;
    const IS_CONTROLLER = 28;
    const IS_BLOCK      = 29;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var array
     */
    protected $info = array();

    /**
     * @return int
     */
    protected function identify()
    {
        $this->info['path'] = $this->getRealPath();
        $pathParts = explode(DIRECTORY_SEPARATOR, $this->getRealPath()); //@todo work on compatible DS
        $k = array_search('app', $pathParts);
        if (array_key_exists($k + 1, $pathParts) && $pathParts[$k + 1] == 'code' &&
            array_key_exists($k + 2, $pathParts) && in_array($pathParts[$k + 2], array('local', 'community', 'core'))) {
            $this->info['codepool'] = $pathParts[$k + 2];
            if (!array_key_exists($k + 3, $pathParts)) {
                return self::IS_CODEPOOL;
            } else {
                $this->info['namespace'] = $pathParts[$k + 3];
                if (!array_key_exists($k + 4, $pathParts)) {
                    return self::IS_NAMESPACE;
                } else {
                    $this->info['module'] = $this->info['namespace'] . '_' . $pathParts[$k + 4];
                    if (!array_key_exists($k + 5, $pathParts)) {
                        return self::IS_MODULE;
                    } else {
                        $this->info['component'] = strtolower($pathParts[$k + 5]);
                        switch ($pathParts[$k + 5]) {
                            case 'Model' :
                                return self::IS_MODEL;
                            case 'Helper' :
                                return self::IS_HELPER;
                            case 'Controller' :
                                return self::IS_CONTROLLER;
                            case 'Block' :
                                return self::IS_BLOCK;
                            default  :
                                return -1;
                        }
                    }
                }
            }
        }
        return -1;
    }

    /**
     * Constructor
     *
     * @param string $file             The file name
     * @param string $relativePath     The relative path
     * @param string $relativePathname The relative path name
     */
    public function __construct($file, $relativePath, $relativePathname)
    {
        parent::__construct($file, $relativePath, $relativePathname);
        $this->id = $this->identify();
    }

    /**
     * @return bool
     */
    public function isModule()
    {
        return $this->isDir() && $this->id === self::IS_MODULE;
    }

    /**
     * @param string $pattern
     * @return FileInfo
     */
    public function getFiles($pattern = '/.*/')
    {
        $finder = new Finder();
        return $finder->files()->in($this->getRealPath())->name($pattern);
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return $this->info['module'];
    }
}
