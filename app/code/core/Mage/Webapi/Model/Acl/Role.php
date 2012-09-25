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
 * Role item model
 *
 * @method int getRoleId()
 * @method string getRoleName()
 * @method Mage_Webapi_Model_Acl_Role setRoleName(string $value)
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Acl_Role extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('Mage_Webapi_Model_Resource_Acl_Role');
    }

    /**
     * Get Web API resources array
     *
     * @return array
     */
    public function getResourcesArray()
    {
        /** @var $acl Magento_Acl */
        $acl = Mage::getModel('Magento_Acl');
        Mage::getModel('Mage_Webapi_Model_Authorization_Loader_Resource')->populateAcl($acl);
        return $acl->getResources();
    }

    /**
     * Get Web API resources XML nodes list
     *
     * @return DOMNodeList
     */
    public function getResourcesList()
    {
        /** @var $config Mage_Webapi_Model_Authorization_Config */
        $config = Mage::getSingleton('Mage_Webapi_Model_Authorization_Config');
        return $config->getAclResources();
    }
}
