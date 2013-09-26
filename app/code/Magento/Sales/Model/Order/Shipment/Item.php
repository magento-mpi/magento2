<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @method Magento_Sales_Model_Resource_Order_Shipment_Item _getResource()
 * @method Magento_Sales_Model_Resource_Order_Shipment_Item getResource()
 * @method int getParentId()
 * @method Magento_Sales_Model_Order_Shipment_Item setParentId(int $value)
 * @method float getRowTotal()
 * @method Magento_Sales_Model_Order_Shipment_Item setRowTotal(float $value)
 * @method float getPrice()
 * @method Magento_Sales_Model_Order_Shipment_Item setPrice(float $value)
 * @method float getWeight()
 * @method Magento_Sales_Model_Order_Shipment_Item setWeight(float $value)
 * @method float getQty()
 * @method int getProductId()
 * @method Magento_Sales_Model_Order_Shipment_Item setProductId(int $value)
 * @method int getOrderItemId()
 * @method Magento_Sales_Model_Order_Shipment_Item setOrderItemId(int $value)
 * @method string getAdditionalData()
 * @method Magento_Sales_Model_Order_Shipment_Item setAdditionalData(string $value)
 * @method string getDescription()
 * @method Magento_Sales_Model_Order_Shipment_Item setDescription(string $value)
 * @method string getName()
 * @method Magento_Sales_Model_Order_Shipment_Item setName(string $value)
 * @method string getSku()
 * @method Magento_Sales_Model_Order_Shipment_Item setSku(string $value)
 */
class Magento_Sales_Model_Order_Shipment_Item extends Magento_Core_Model_Abstract
{
    protected $_eventPrefix = 'sales_shipment_item';
    protected $_eventObject = 'shipment_item';

    protected $_shipment = null;
    protected $_orderItem = null;

    /**
     * @var Magento_Sales_Model_Order_ItemFactory
     */
    protected $_orderItemFactory;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Sales_Model_Order_ItemFactory $orderItemFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Sales_Model_Order_ItemFactory $orderItemFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_orderItemFactory = $orderItemFactory;
    }

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Resource_Order_Shipment_Item');
    }

    /**
     * Declare Shipment instance
     *
     * @param   Magento_Sales_Model_Order_Shipment $shipment
     * @return  Magento_Sales_Model_Order_Shipment_Item
     */
    public function setShipment(Magento_Sales_Model_Order_Shipment $shipment)
    {
        $this->_shipment = $shipment;
        return $this;
    }

    /**
     * Retrieve Shipment instance
     *
     * @return Magento_Sales_Model_Order_Shipment
     */
    public function getShipment()
    {
        return $this->_shipment;
    }

    /**
     * Declare order item instance
     *
     * @param   Magento_Sales_Model_Order_Item $item
     * @return  Magento_Sales_Model_Order_Shipment_Item
     */
    public function setOrderItem(Magento_Sales_Model_Order_Item $item)
    {
        $this->_orderItem = $item;
        $this->setOrderItemId($item->getId());
        return $this;
    }

    /**
     * Retrieve order item instance
     *
     * @return Magento_Sales_Model_Order_Item
     */
    public function getOrderItem()
    {
        if (null === $this->_orderItem) {
            if ($this->getShipment()) {
                $this->_orderItem = $this->getShipment()->getOrder()->getItemById($this->getOrderItemId());
            } else {
                $this->_orderItem = $this->_orderItemFactory->create()->load($this->getOrderItemId());
            }
        }
        return $this->_orderItem;
    }

    /**
     * Declare qty
     *
     * @param float $qty
     * @return Magento_Sales_Model_Order_Invoice_Item
     * @throws Magento_Core_Exception
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
            throw new Magento_Core_Exception(
                __('We found an invalid qty to ship for item "%1".', $this->getName())
            );
        }
        return $this;
    }

    /**
     * Applying qty to order item
     *
     * @return Magento_Sales_Model_Order_Shipment_Item
     */
    public function register()
    {
        $this->getOrderItem()->setQtyShipped(
            $this->getOrderItem()->getQtyShipped() + $this->getQty()
        );
        return $this;
    }

    /**
     * Before object save
     *
     * @return Magento_Sales_Model_Order_Shipment_Item
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
