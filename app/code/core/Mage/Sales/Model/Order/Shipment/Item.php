<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Sales_Model_Order_Shipment_Item extends Mage_Core_Model_Abstract
{
    protected $_shipment = null;
    protected $_orderItem = null;

    /**
     * Initialize resource model
     */
    function _construct()
    {
        $this->_init('sales/order_shipment_item');
    }

    /**
     * Declare Shipment instance
     *
     * @param   Mage_Sales_Model_Order_Shipment $shipment
     * @return  Mage_Sales_Model_Order_Shipment_Item
     */
    public function setShipment(Mage_Sales_Model_Order_Shipment $shipment)
    {
        $this->_shipment = $shipment;
        return $this;
    }

    /**
     * Retrieve Shipment instance
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function getShipment()
    {
        return $this->_shipment;
    }

    /**
     * Declare order item instance
     *
     * @param   Mage_Sales_Model_Order_Item $item
     * @return  Mage_Sales_Model_Order_Shipment_Item
     */
    public function setOrderItem(Mage_Sales_Model_Order_Item $item)
    {
        $this->_orderItem = $item;
        $this->setOrderItemId($item->getId());
        return $this;
    }

    /**
     * Retrieve order item instance
     *
     * @return Mage_Sales_Model_Order_Item
     */
    public function getOrderItem()
    {
        if (is_null($this->_orderItem)) {
            $this->_orderItem = Mage::getModel('sales/order_item')
                ->load($this->getOrderItemId());
        }
        return $this->_orderItem;
    }

    /**
     * Applying qty to order item
     *
     * @return Mage_Sales_Model_Order_Shipment_Item
     */
    public function applyQty()
    {
        $this->getOrderItem()->setQtyShipped(
            $this->getOrderItem()->getQtyShipped()+$this->getQty()
        );
        return $this;
    }

}