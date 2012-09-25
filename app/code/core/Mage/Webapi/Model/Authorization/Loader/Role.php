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
 * Api Acl Role Loader
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Authorization_Loader_Role implements Magento_Acl_Loader
{
    /**
     * @var Mage_Webapi_Model_Resource_Acl_Role
     */
    protected $_resourceModel;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_resourceModel = isset($data['resourceModel']) ?
            $data['resourceModel'] : Mage::getResourceModel('Mage_Webapi_Model_Resource_Acl_Role');
        $this->_config = isset($data['config']) ? $data['config'] : Mage::getConfig();
    }

    /**
     * Populate ACL with roles from external storage
     *
     * @param Magento_Acl $acl
     */
    public function populateAcl(Magento_Acl $acl)
    {
        $roleList = $this->_resourceModel->getRolesIds();
        foreach ($roleList as $roleId) {
            /** @var $aclRole Mage_Webapi_Model_Authorization_Role */
            $aclRole = $this->_config->getModelInstance('Mage_Webapi_Model_Authorization_Role', $roleId);
            $acl->addRole($aclRole);
            //Deny all privileges to Role. Some of them could be allowed later by whitelist
            $acl->deny($aclRole);
        }
    }
}
