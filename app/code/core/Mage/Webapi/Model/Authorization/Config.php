<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Api Acl Config model
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Authorization_Config implements Mage_Core_Model_Acl_Config_ConfigInterface
{

    CONST ACL_RESOURCES_XPATH = '/config/acl/resources/*';

    CONST ACL_VIRTUAL_RESOURCES_XPATH = '/config/mapping/*';

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Magento_Acl_Config_Reader
     */
    protected $_reader;

    public function __construct(array $args = array())
    {
        $this->_config = isset($args['config']) ? $args['config'] : Mage::getConfig();
    }

    /**
     * Retrieve list of acl files from each module
     *
     * @return array
     */
    protected function _getAclResourceFiles()
    {
        $files = $this->_config
            ->getModuleConfigurationFiles('webapi' . DIRECTORY_SEPARATOR . 'acl.xml');
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
            $this->_reader = $this->_config
                ->getModelInstance('Mage_Webapi_Model_Authorization_Config_Reader', $aclResourceFiles);
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
