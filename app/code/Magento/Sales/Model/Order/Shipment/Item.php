<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Order\Shipment;

/**
 * @method \Magento\Sales\Model\Resource\Order\Shipment\Item _getResource()
 * @method \Magento\Sales\Model\Resource\Order\Shipment\Item getResource()
 * @method int getParentId()
 * @method \Magento\Sales\Model\Order\Shipment\Item setParentId(int $value)
 * @method float getRowTotal()
 * @method \Magento\Sales\Model\Order\Shipment\Item setRowTotal(float $value)
 * @method float getPrice()
 * @method \Magento\Sales\Model\Order\Shipment\Item setPrice(float $value)
 * @method float getWeight()
 * @method \Magento\Sales\Model\Order\Shipment\Item setWeight(float $value)
 * @method float getQty()
 * @method int getProductId()
 * @method \Magento\Sales\Model\Order\Shipment\Item setProductId(int $value)
 * @method int getOrderItemId()
 * @method \Magento\Sales\Model\Order\Shipment\Item setOrderItemId(int $value)
 * @method string getAdditionalData()
 * @method \Magento\Sales\Model\Order\Shipment\Item setAdditionalData(string $value)
 * @method string getDescription()
 * @method \Magento\Sales\Model\Order\Shipment\Item setDescription(string $value)
 * @method string getName()
 * @method \Magento\Sales\Model\Order\Shipment\Item setName(string $value)
 * @method string getSku()
 * @method \Magento\Sales\Model\Order\Shipment\Item setSku(string $value)
 */
class Item extends \Magento\Model\AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'sales_shipment_item';

    /**
     * @var string
     */
    protected $_eventObject = 'shipment_item';

    /**
     * @var \Magento\Sales\Model\Order\Shipment|null
     */
    protected $_shipment = null;

    /**
     * @var \Magento\Sales\Model\Order\Item|null
     */
    protected $_orderItem = null;

    /**
     * @var \Magento\Sales\Model\Order\ItemFactory
     */
    protected $_orderItemFactory;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Sales\Model\Order\ItemFactory $orderItemFactory
     * @param \Magento\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        \Magento\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_orderItemFactory = $orderItemFactory;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Resource\Order\Shipment\Item');
    }

    /**
     * Declare Shipment instance
     *
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return $this
     */
    public function setShipment(\Magento\Sales\Model\Order\Shipment $shipment)
    {
        $this->_shipment = $shipment;
        return $this;
    }

    /**
     * Retrieve Shipment instance
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getShipment()
    {
        return $this->_shipment;
    }

    /**
     * Declare order item instance
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @return $this
     */
    public function setOrderItem(\Magento\Sales\Model\Order\Item $item)
    {
        $this->_orderItem = $item;
        $this->setOrderItemId($item->getId());
        return $this;
    }

    /**
     * Retrieve order item instance
     *
     * @return \Magento\Sales\Model\Order\Item
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
     * @return \Magento\Sales\Model\Order\Invoice\Item
     * @throws \Magento\Model\Exception
     */
    public function setQty($qty)
    {
        if ($this->getOrderItem()->getIsQtyDecimal()) {
            $qty = (double)$qty;
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
            throw new \Magento\Model\Exception(__('We found an invalid qty to ship for item "%1".', $this->getName()));
        }
        return $this;
    }

    /**
     * Applying qty to order item
     *
     * @return $this
     */
    public function register()
    {
        $this->getOrderItem()->setQtyShipped($this->getOrderItem()->getQtyShipped() + $this->getQty());
        return $this;
    }

    /**
     * Before object save
     *
     * @return $this
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
