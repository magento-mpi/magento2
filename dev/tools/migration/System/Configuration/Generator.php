<?php

class Tools_Migration_System_Configuration_Generator
{
    /**
     * @var Tools_Migration_System_FileManager
     */
    protected $_fileManager;

    public function __construct(Tools_Migration_System_FileManager $fileManager)
    {

    }

    /**
     *
     * @param string $fileName
     * @param array $configuration
     */
    public function createConfiguration($fileName, array $configuration)
    {
        $this->_fileManager->write($this->_createDOMDocument($configuration)->saveXML(), $this->_getPathToSave($fileName));
    }

    /**
     * @param array $configuration
     * @return DOMDocument
     */
    protected function _createDOMDocument(array $configuration)
    {
        return new DOMDocument();
    }

    /**
     * @param $fileName
     * @return string
     */
    protected function _getPathToSave($fileName)
    {
        return '';
    }
}
