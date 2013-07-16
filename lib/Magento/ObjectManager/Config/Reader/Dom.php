<?php
/**
 * ObjectManager DOM configuration reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_ObjectManager_Config_Reader_Dom extends Magento_Config_XmlAbstract
{
    /**
     * @var Magento_ObjectManager_Config_Mapper_Dom
     */
    protected $_mapper;

    /**
     * @var bool
     */
    protected $_isRuntimeValidated;

    /**
     * @param array $configFiles
     * @param Magento_ObjectManager_Config_Mapper_Dom $mapper
     * @param bool $isRuntimeValidated
     */
    public function __construct(
        array $configFiles,
        Magento_ObjectManager_Config_Mapper_Dom $mapper = null,
        $isRuntimeValidated = false
    ) {
        $this->_isRuntimeValidated = $isRuntimeValidated;
        $this->_mapper = $mapper ?: new Magento_ObjectManager_Config_Mapper_Dom();
        $this->_merge($configFiles);

    }

    /**
     * Get absolute path to the XML-schema file
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return realpath(__DIR__ . '/../../etc/') . DIRECTORY_SEPARATOR . 'config.xsd';
    }

    /**
     * Extract configuration data from the DOM structure
     *
     * @param DOMDocument $dom
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _extractData(DOMDocument $dom)
    {
        return array();
    }

    /**
     * Get if xml files must be runtime validated
     *
     * @return bool
     */
    protected function _isRuntimeValidated()
    {
        return $this->_isRuntimeValidated;
    }

    /**
     * Get XML-contents, initial for merging
     *
     * @return string
     */
    protected function _getInitialXml()
    {
        return '<?xml version="1.0" encoding="utf-8"?><config />';
    }

    /**
     * Get list of paths to identifiable nodes
     *
     * @return array
     */
    protected function _getIdAttributes()
    {
        return array(
            '/config/preference'         => 'for',
            '/config/type'               => 'name',
            '/config/type/param'         => 'name',
            '/config/type/plugin'        => 'name',
            '/config/virtualType'        => 'name',
            '/config/virtualType/param'  => 'name',
            '/config/virtualType/plugin' => 'name',
        );
    }

    /**
     * Read di configuration
     *
     * @return array
     */
    public function read()
    {
        return $this->_mapper->map($this->_getDomConfigModel()->getDom());
    }
}
