<?php

class Tools_Migration_System_Configuration_Reader
{

    protected $_fileManager;

    protected $_parser;

    protected $_mapper;

    protected $_basePath;

    CONST SYSTEM_CONFIG_PATH_PATTERN = 'app/code/*/*/*/etc/system.xml';

    public function __construct(
        Tools_Migration_System_FileManager $fileManager,
        Tools_Migration_System_Configuration_Parser $parser,
        Tools_Migration_System_Configuration_Mapper $mapper
    )
    {
        $this->_fileManager = $fileManager;
        $this->_parser = $parser;
        $this->_mapper = $mapper;

        $this->_basePath = realpath(dirname(__FILE__) . '/../../../../..');
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        $files = glob(
            $this->_basePath . DIRECTORY_SEPARATOR
            . Tools_Migration_System_Configuration_Reader::SYSTEM_CONFIG_PATH_PATTERN
        );
        $result = array();
        foreach ($files as $fileName) {
            $result[$fileName] = $this->_mapper->transform(
                $this->_parser->parse(
                    $this->_getDOMDocument(
                        $this->_fileManager->getContents($fileName)
                    )
                )
            );
        }

        return $result;
    }

    protected function _getDOMDocument($xml)
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        return $dom;
    }
}
