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
 * Api Role Resource Collection
 *
 * @category    Magento
 * @package     Magento_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Api_Model_Resource_Role_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Api_Model_Role', 'Magento_Api_Model_Resource_Role');
    }

    /**
     * Aet user filter
     *
     * @param int $userId
     * @return Magento_Api_Model_Resource_Role_Collection
     */
    public function setUserFilter($userId)
    {
        $this->addFieldToFilter('user_id', $userId);
        $this->addFieldToFilter('role_type', Magento_Api_Model_Acl::ROLE_TYPE_GROUP);
        return $this;
    }

    /**
     * Set roles filter
     *
     * @return Magento_Api_Model_Resource_Role_Collection
     */
    public function setRolesFilter()
    {
        $this->addFieldToFilter('role_type', Magento_Api_Model_Acl::ROLE_TYPE_GROUP);
        return $this;
    }
}
