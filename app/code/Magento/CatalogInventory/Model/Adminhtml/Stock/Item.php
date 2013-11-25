<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Inventory Stock Model for adminhtml area
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogInventory\Model\Adminhtml\Stock;

class Item extends \Magento\CatalogInventory\Model\Stock\Item
{
    /**
     * Getter for customer group id, return default group if not set
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        if ($this->_customerGroupId === null) {
            return \Magento\Customer\Model\Group::CUST_GROUP_ALL;
        }
        return $this->_customerGroupId;
    }

    /**
     * Check if qty check can be skipped. Skip checking in adminhtml area
     *
     * @return bool
     */
    protected function _isQtyCheckApplicable()
    {
        return false;
    }

    /**
     * Check if notification message should be added despite of backorders notification flag
     *
     * @return string
     */
    protected function _hasDefaultNotificationMessage()
    {
        return true;
    }
}
