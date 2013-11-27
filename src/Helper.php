<?php

namespace Ecg\MagentoFinder;

use PHPParser_Lexer,
    PHPParser_Parser,
    PHPParser_Serializer_XML,
    RuntimeException,
    SimpleXMLElement,
    InvalidArgumentException;

class Helper
{
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
        $parser     = new PHPParser_Parser(new PHPParser_Lexer());
        $serializer = new PHPParser_Serializer_XML();
        $this->xml  = new SimpleXMLElement($serializer->serialize($parser->parse(file_get_contents($mageClassPath))));

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
}
