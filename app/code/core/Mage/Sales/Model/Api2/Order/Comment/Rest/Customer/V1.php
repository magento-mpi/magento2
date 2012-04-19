<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * API2 class for sales order comments (customer)
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Api2_Order_Comment_Rest_Customer_V1 extends Mage_Sales_Model_Api2_Order_Comment_Rest
{
    /**
     * Load order by id
     *
     * @param int $id
     * @throws Mage_Api2_Exception
     * @return Mage_Sales_Model_Order
     */
    protected function _loadOrderById($id)
    {
        $order = parent::_loadOrderById($id);

        // Check sales order's owner
        if ($this->getApiUser()->getUserId() !== $order->getCustomerId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $order;
    }

    /**
     * Retrieve collection instances
     *
     * @return Mage_Sales_Model_Resource_Order_Status_History_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        $collection = parent::_getCollectionForRetrieve();
        $collection->addFieldToFilter('is_visible_on_front', 1);

        return $collection;
    }
}
