<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * ACL role resource
 *
 * @category    Magento
 * @package     Magento_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Api_Model_Resource_Acl_Role extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('api_role', 'role_id');
    }

    /**
     * Action before save
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Api_Model_Resource_Acl_Role
     */
    protected function _beforeSave(Magento_Core_Model_Abstract $object)
    {
        if (!$object->getId()) {
            $this->setCreated(Mage::getSingleton('Magento_Core_Model_Date')->gmtDate());
        }
        return $this;
    }
}
