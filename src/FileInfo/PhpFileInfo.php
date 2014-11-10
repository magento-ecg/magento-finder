<?php

namespace Ecg\MagentoFinder\FileInfo;

use Ecg\MagentoFinder\FileInfo,
    Exception,
    PhpParser,
    SimpleXMLElement,
    SebastianBergmann\PHPLOC\Analyser;

class PhpFileInfo extends FileInfo implements PhpFileInfoInterface, PhpClassInfoInterface
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
        $parser        = new PhpParser\Parser(new PhpParser\Lexer\Emulative);
        $traverser     = new PhpParser\NodeTraverser;
        libxml_use_internal_errors(true);
        try {
            $this->xml  = new SimpleXMLElement($traverser->traverse($parser->parse($this->getContents())));
        } catch (Exception $e) {
            $this->xml = new SimpleXMLElement('<?xml version="1.0"?><dummy></dummy>');
        }
    }

    /**
     * @return array
     */
    public function getCodeMetrics()
    {
        $analyzer = new Analyser();
        return $analyzer->countFiles(array($this->getRelativePath()), null);
    }

    /**
     *
     */
    public function getDispatchedEvents()
    {
        // TODO: Implement getDispatchedEvents() method.
    }

    /**
     * @return string
     */
    public function getParentClassName()
    {
        $res = $this->xml->xpath('//node:Stmt_Class/subNode:extends/node:Name//scalar:string');
        return $res ? (string)$res[0] : '';
    }

    public function getInterfaceNames()
    {
        // TODO: Implement getInterfaceNames() method.
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        $res = $this->xml->xpath('//node:Stmt_Class/subNode:name/scalar:string');
        return $res ? (string)$res[0] : '';
    }
}
