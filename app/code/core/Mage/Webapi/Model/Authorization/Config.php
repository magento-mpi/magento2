<?php
/**
 * Api Acl Config model
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Authorization_Config implements Mage_Core_Model_Acl_Config_ConfigInterface
{

    const ACL_RESOURCES_XPATH = '/config/acl/resources/*';

    const ACL_VIRTUAL_RESOURCES_XPATH = '/config/mapping/*';

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Magento_Acl_Config_Reader
     */
    protected $_reader;

    /**
     * @var Mage_Webapi_Model_Authorization_Config_ReaderFactory
     */
    protected $_readerFactory;

    /**
     * @param Mage_Core_Model_Config $config
     * @param Mage_Webapi_Model_Authorization_Config_ReaderFactory $readerFactory
     */
    public function __construct(Mage_Core_Model_Config $config,
        Mage_Webapi_Model_Authorization_Config_ReaderFactory $readerFactory
    ) {
        $this->_config = $config;
        $this->_readerFactory = $readerFactory;
    }

    /**
     * Retrieve list of acl files from each module
     *
     * @return array
     */
    protected function _getAclResourceFiles()
    {
        $files = $this->_config->getModuleConfigurationFiles('webapi' . DIRECTORY_SEPARATOR . 'acl.xml');
        return (array)$files;
    }

    /**
     * Reader object initialization
     *
     * @return Magento_Acl_Config_Reader
     */
    protected function _getReader()
    {
        if (is_null($this->_reader)) {
            $aclResourceFiles = $this->_getAclResourceFiles();
            $this->_reader = $this->_readerFactory->createReader(array($aclResourceFiles));
        }
        return $this->_reader;
    }

    /**
     * Get DOMXPath with loaded resources inside
     *
     * @return DOMXPath
     */
    protected function _getXPathResources()
    {
        $aclResources = $this->_getReader()->getAclResources();
        return new DOMXPath($aclResources);
    }

    /**
     * Return ACL Resources
     *
     * @return DOMNodeList
     */
    public function getAclResources()
    {
        return $this->_getXPathResources()->query(self::ACL_RESOURCES_XPATH);
    }

    /**
     * Return ACL Virtual Resources
     *
     * Virtual resources are not shown in resource list, they use existing resource to check permission
     *
     * @return DOMNodeList
     */
    public function getAclVirtualResources()
    {
        return $this->_getXPathResources()->query(self::ACL_VIRTUAL_RESOURCES_XPATH);
    }
}
