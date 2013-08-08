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
 * Web API Rules Resource Collection.
 *
 * @category    Mage
 * @package     Mage_Webpi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Resource_Acl_Rule_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization.
     */
    protected function _construct()
    {
        $this->_init('Mage_Webapi_Model_Acl_Rule', 'Mage_Webapi_Model_Resource_Acl_Rule');
    }

    /**
     * Retrieve rules by role.
     *
     * @param int $roleId
     * @return Mage_Webapi_Model_Resource_Acl_Rule_Collection
     */
    public function getByRole($roleId)
    {
        $this->getSelect()->where("role_id = ?", (int)$roleId);
        return $this;
    }
}
