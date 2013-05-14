<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Admin role data grid collection
 *
 * @category    Mage
 * @package     Mage_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_User_Model_Resource_Role_Grid_Collection extends Mage_User_Model_Resource_Role_Collection
{
    /**
     * Prepare select for load
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFieldToFilter('role_type', 'G');
        return $this;
    }
}
