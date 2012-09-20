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
 * Backend Acl Config model
 *
 * @category    Mage
 * @package     Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Authorization_Config
{

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
        return (array) $files;
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
            $this->_reader = $this->_config->getModelInstance('Magento_Acl_Config_Reader', $aclResourceFiles);
        }
        return $this->_reader;
    }

    /**
     * Return ACL Resources loaded from cache if enabled or from files merged previously
     *
     * @return DOMNodeList
     */
    public function getAclResources()
    {
        $aclResources = $this->_getReader()->getAclResources();
        $xpath = new DOMXPath($aclResources);
        return $xpath->query('/config/acl/resources/resource[@id="Mage_Webapi"]/*');
    }
}
