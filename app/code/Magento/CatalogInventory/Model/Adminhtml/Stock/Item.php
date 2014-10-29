<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model\Adminhtml\Stock;

/**
 * Catalog Inventory Stock Model for adminhtml area
 */
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
            return \Magento\Customer\Service\V1\CustomerGroupServiceInterface::CUST_GROUP_ALL;
        }
        return parent::getCustomerGroupId();
    }

    /**
     * Check if qty check can be skipped. Skip checking in adminhtml area
     *
     * @return bool
     */
    protected function _isQtyCheckApplicable()
    {
        return true;
    }

    /**
     * Check if notification message should be added despite of backorders notification flag
     *
     * @return bool
     */
    protected function _hasDefaultNotificationMessage()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function hasAdminArea()
    {
        return true;
    }
}
