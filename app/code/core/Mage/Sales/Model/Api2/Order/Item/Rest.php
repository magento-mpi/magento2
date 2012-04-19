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
 * Abstract API2 class for order items rest
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Sales_Model_Api2_Order_Item_Rest extends Mage_Sales_Model_Api2_Order_Item
{
    /**#@+
     * Parameters in request used in model (usually specified in route)
     */
    const PARAM_ORDER_ID = 'id';
    /**#@-*/

    /**
     * Get order items list
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $data = array();
        /* @var $item Mage_Sales_Model_Order_Item */
        foreach ($this->_getCollectionForRetrieve() as $item) {
            $itemData = $item->getData();
            $itemData['status'] = $item->getStatus();
            $data[] = $itemData;
        }
        return $data;
    }
    /**
     * Retrieve order items collection
     *
     * @return Mage_Sales_Model_Resource_Order_Item_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = $this->_loadOrderById(
            $this->getRequest()->getParam(self::PARAM_ORDER_ID)
        );

        /* @var $collection Mage_Sales_Model_Resource_Order_Item_Collection */
        $collection = Mage::getResourceModel('Mage_Sales_Model_Resource_Order_Item_Collection');
        $collection->setOrderFilter($order->getId());
        $this->_applyCollectionModifiers($collection);
        return $collection;
    }

    /**
     * Load order by id
     *
     * @param int $id
     * @throws Mage_Api2_Exception
     * @return Mage_Sales_Model_Order
     */
    protected function _loadOrderById($id)
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = Mage::getModel('Mage_Sales_Model_Order')->load($id);
        if (!$order->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $order;
    }
}
