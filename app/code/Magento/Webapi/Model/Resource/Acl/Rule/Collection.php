<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Web API Rules Resource Collection.
 *
 * @category    Magento
 * @package     Magento_Webapi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Webapi_Model_Resource_Acl_Rule_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization.
     */
    protected function _construct()
    {
        $this->_init('Magento_Webapi_Model_Acl_Rule', 'Magento_Webapi_Model_Resource_Acl_Rule');
    }

    /**
     * Retrieve rules by role.
     *
     * @param int $roleId
     * @return Magento_Webapi_Model_Resource_Acl_Rule_Collection
     */
    public function getByRole($roleId)
    {
        $this->getSelect()->where("role_id = ?", (int)$roleId);
        return $this;
    }
}
