<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @method Mage_Sales_Model_Resource_Order_Shipment_Item _getResource()
 * @method Mage_Sales_Model_Resource_Order_Shipment_Item getResource()
 * @method int getParentId()
 * @method Mage_Sales_Model_Order_Shipment_Item setParentId(int $value)
 * @method float getRowTotal()
 * @method Mage_Sales_Model_Order_Shipment_Item setRowTotal(float $value)
 * @method float getPrice()
 * @method Mage_Sales_Model_Order_Shipment_Item setPrice(float $value)
 * @method float getWeight()
 * @method Mage_Sales_Model_Order_Shipment_Item setWeight(float $value)
 * @method float getQty()
 * @method int getProductId()
 * @method Mage_Sales_Model_Order_Shipment_Item setProductId(int $value)
 * @method int getOrderItemId()
 * @method Mage_Sales_Model_Order_Shipment_Item setOrderItemId(int $value)
 * @method string getAdditionalData()
 * @method Mage_Sales_Model_Order_Shipment_Item setAdditionalData(string $value)
 * @method string getDescription()
 * @method Mage_Sales_Model_Order_Shipment_Item setDescription(string $value)
 * @method string getName()
 * @method Mage_Sales_Model_Order_Shipment_Item setName(string $value)
 * @method string getSku()
 * @method Mage_Sales_Model_Order_Shipment_Item setSku(string $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Shipment_Item extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'sales_shipment_item';
    protected $_eventObject = 'shipment_item';

    protected $_shipment = null;
    protected $_orderItem = null;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Mage_Sales_Model_Resource_Order_Shipment_Item');
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
        if (null === $this->_orderItem) {
            if ($this->getShipment()) {
                $this->_orderItem = $this->getShipment()->getOrder()->getItemById($this->getOrderItemId());
            } else {
                $this->_orderItem = Mage::getModel('Mage_Sales_Model_Order_Item')
                    ->load($this->getOrderItemId());
            }
        }
        return $this->_orderItem;
    }

    /**
     * Declare qty
     *
     * @param   float $qty
     * @return  Mage_Sales_Model_Order_Invoice_Item
     */
    public function setQty($qty)
    {
        if ($this->getOrderItem()->getIsQtyDecimal()) {
            $qty = (float)$qty;
        } else {
            $qty = (int)$qty;
        }
        $qty = $qty > 0 ? $qty : 0;
        /**
         * Check qty availability
         */
        if ($qty <= $this->getOrderItem()->getQtyToShip() || $this->getOrderItem()->isDummy(true)) {
            $this->setData('qty', $qty);
        } else {
            Mage::throwException(
                Mage::helper('Mage_Sales_Helper_Data')
                    ->__('We found an invalid qty to ship for item "%s".', $this->getName())
            );
        }
        return $this;
    }

    /**
     * Applying qty to order item
     *
     * @return Mage_Sales_Model_Order_Shipment_Item
     */
    public function register()
    {
        $this->getOrderItem()->setQtyShipped(
            $this->getOrderItem()->getQtyShipped()+$this->getQty()
        );
        return $this;
    }

    /**
     * Before object save
     *
     * @return Mage_Sales_Model_Order_Shipment_Item
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getShipment()) {
            $this->setParentId($this->getShipment()->getId());
        }
        return $this;
    }
}
