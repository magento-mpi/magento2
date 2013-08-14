<?php
/**
 * API ACL Config Reader model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Acl_Loader_Resource_ConfigReader extends Magento_Acl_Loader_Resource_ConfigReader_Xml
{
    const ACL_VIRTUAL_RESOURCES_XPATH = '/config/mapping/*';

    /**
     * Application config
     *
     * @var Magento_Core_Model_Config
     */
    protected $_config;

    /**
     * @param Magento_Webapi_Model_Acl_Loader_Resource_ConfigReader_FileList $fileList
     * @param Magento_Acl_Loader_Resource_ConfigReader_Xml_ArrayMapper $mapper
     * @param Magento_Config_Dom_Converter_ArrayConverter $converter
     * @param Magento_Core_Model_Config $config
     */
    public function __construct(
        Magento_Webapi_Model_Acl_Loader_Resource_ConfigReader_FileList $fileList,
        Magento_Acl_Loader_Resource_ConfigReader_Xml_ArrayMapper $mapper,
        Magento_Config_Dom_Converter_ArrayConverter $converter,
        Magento_Core_Model_Config $config
    ) {
        if (count($fileList->asArray())) {
            parent::__construct($fileList, $mapper, $converter);
        } else {
            $this->_mapper = $mapper;
            $this->_converter = $converter;
        }

        $this->_config = $config;
    }

    /**
     * Get absolute path to the XML-schema file.
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return $this->_config->getModuleDir('etc', 'Magento_Webapi') . DIRECTORY_SEPARATOR . 'acl.xsd';
    }

    /**
     * Get XML-contents, initial for merging.
     *
     * @return string
     */
    protected function _getInitialXml()
    {
        return '<?xml version="1.0" encoding="utf-8"?><config><acl></acl><mapping></mapping></config>';
    }

    /**
     * Return ACL Virtual Resources.
     *
     * Virtual resources are not shown in resource list, they use existing resource to check permission.
     *
     * @return DOMNodeList
     */
    public function getAclVirtualResources()
    {
        $xpath = new DOMXPath($this->_getDomConfigModel()->getDom());
        return $xpath->query(self::ACL_VIRTUAL_RESOURCES_XPATH);
    }
}
