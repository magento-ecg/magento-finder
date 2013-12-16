<?php

namespace Ecg\MagentoFinder\FileInfo;

use Ecg\MagentoFinder\FileInfo,
    PHPParser_Lexer,
    PHPParser_Parser,
    PHPParser_Serializer_XML,
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
        $parser     = new PHPParser_Parser(new PHPParser_Lexer());
        $serializer = new PHPParser_Serializer_XML();
        $this->xml  = new SimpleXMLElement($serializer->serialize($parser->parse($this->getContents())));
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
