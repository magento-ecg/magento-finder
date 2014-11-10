<?php

namespace Ecg\MagentoFinder;

use PhpParser,
    RuntimeException,
    SimpleXMLElement,
    InvalidArgumentException;

class Helper
{
    const APP_DEPTH       = 1;
    const CODE_DEPTH      = 2;
    const CODEPOOL_DEPTH  = 3;
    const NAMESPACE_DEPTH = 4;
    const MODULE_DEPTH    = 5;
    const COMPONENT_DEPTH = 6;

    const MAGE_VERSION_PART_NODE_XPATH = <<<XPATH
//node:Stmt_ClassMethod/subNode:name[scalar:string="getVersionInfo"]/preceding::node:Stmt_Return
//node:Expr_ArrayItem[subNode:key//subNode:value[scalar:string="%s"]]/subNode:value//subNode:value/scalar:string
XPATH;

    /**
     * @var SimpleXMLElement
     */
    protected $xml;

    /**
     * @param $part
     * @return string
     * @throws RuntimeException
     */
    protected function getVersionPart($part)
    {
        $item = $this->xml->xpath(sprintf(self::MAGE_VERSION_PART_NODE_XPATH, $part));
        if (empty($item)) {
            throw new RuntimeException('Failed to fetch Magento version from the Mage.php file.');
        }
        return $item[0]->__toString();
    }

    /**
     * @param $mageClassPath
     * @return string
     */
    public function getMagentoVersion($mageClassPath)
    {
        $parser     = new PhpParser\Parser(new PhpParser\Lexer\Emulative);
        $traverser  = new PhpParser\NodeTraverser;
        $this->xml  = new SimpleXMLElement($traverser->traverse($parser->parse(file_get_contents($mageClassPath))));

        $version = array(
            $this->getVersionPart('major'),
            $this->getVersionPart('minor'),
            $this->getVersionPart('revision'),
            $this->getVersionPart('patch')
        );

        return implode('.', $version);
    }

    /**
     * @param $path
     * @param $codepool
     * @throws \InvalidArgumentException
     * @return string
     */
    public function getCodepoolPathByName($path, $codepool)
    {
        if (!in_array($codepool, array('local', 'core', 'community'))) {
            throw new InvalidArgumentException(sprintf('Invalid codepool %s.', $codepool));
        }

        if (is_dir(sprintf('%s/app/code/%s', $path, $codepool))) {
            return sprintf('%s/app/code/%s', $path, $codepool);
        } elseif (is_dir(sprintf('%s/%s', $path, $codepool))) {
            return sprintf('%s/%s', $path, $codepool);
        }
        throw new InvalidArgumentException(sprintf('Cannot find codepool %s within the specified path', $codepool));
    }

    /**
     * @param $path
     * @param $moduleName
     * @throws \InvalidArgumentException
     * @return string
     */
    public function getModulePathByName($path, $moduleName)
    {
        $parts = explode('_', $moduleName);
        if (count($parts) != 2) {
            throw new InvalidArgumentException('Incorrect module name');
        }

        if (is_dir(sprintf('%s/%s/%s', $path, $parts[0], $parts[1]))) {
            return sprintf('%s/%s/%s', $path, $parts[0], $parts[1]);
        }

        foreach (array('local', 'core', 'community') as $codepool) {
            if (is_dir(sprintf('%s/app/code/%s/%s/%s', $path, $codepool, $parts[0], $parts[1]))) {
                return sprintf('%s/app/code/%s/%s/%s', $path, $codepool, $parts[0], $parts[1]);
            } elseif (is_dir(sprintf('%s/%s/%s', $path, $parts[0], $parts[1]))) {
                return sprintf('%s/%s/%s', $path, $parts[0], $parts[1]);
            }
        }
        throw new InvalidArgumentException(sprintf('Cannot find module %s within the specified path', $moduleName));
    }

    /**
     * @param $path
     * @return array|bool
     */
    public function getMagePathParts($path)
    {
        $pathParts = explode(DIRECTORY_SEPARATOR, trim($path, DIRECTORY_SEPARATOR));
        $k = array_search('app', $pathParts);
        if ($k === false) return is_dir($path . DIRECTORY_SEPARATOR . 'app') ? array() : false;
        return array_slice($pathParts, $k);
    }

    /**
     *            0     1    2    3             4         5        6
     * path/to/magento/app/code/[codepool]/[namespace]/[module]/[component]
     *
     * [codepool]  = local | community | core
     * [component] = model | helper | controller | block | data | sql | etc
     *
     * @param $path
     * @return int
     */
    public function getCurrentDepth($path)
    {
        $parts = $this->getMagePathParts($path);
        return $parts !== false ? count($parts) : -1;
    }

    public function ucWords($str, $destSep='_', $srcSep='_')
    {
        return str_replace(' ', $destSep, ucwords(str_replace($srcSep, ' ', $str)));
    }
}
