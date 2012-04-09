<?php
/**
 * {license}
 *
 * @category    Mage
 * @package     Mage_Sales
 */

/**
 * Abstract API2 class for sales order comments
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Sales_Model_Api2_Order_Comments_Rest extends Mage_Sales_Model_Api2_Order_Comments
{
    /**#@+
     * Parameters in request used in model (usually specified in route mask)
     */
    const PARAM_ORDER_ID = 'id';
    /**#@-*/

    /**
     * Get sales order comments
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $collection = $this->_getCollectionForRetrieve();
        $collection->addFieldToSelect($this->getForcedAttributes());

        $this->_applyCollectionModifiers($collection);

        $data = $collection->load()->toArray();
        return isset($data['items']) ? $data['items'] : $data;
    }

    /**
     * Retrieve collection instances
     *
     * @return Mage_Sales_Model_Resource_Order_Status_History_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        /* @var $collection Mage_Sales_Model_Resource_Order_Status_History_Collection */
        $collection = Mage::getResourceModel('Mage_Sales_Model_Resource_Order_Status_History_Collection');
        $collection->setOrderFilter($this->_loadOrderById($this->getRequest()->getParam(self::PARAM_ORDER_ID)));

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
