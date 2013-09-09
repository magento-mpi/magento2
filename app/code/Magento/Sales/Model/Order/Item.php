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
 * Order Item Model
 *
 * @method Magento_Sales_Model_Resource_Order_Item _getResource()
 * @method Magento_Sales_Model_Resource_Order_Item getResource()
 * @method int getOrderId()
 * @method Magento_Sales_Model_Order_Item setOrderId(int $value)
 * @method int getParentItemId()
 * @method Magento_Sales_Model_Order_Item setParentItemId(int $value)
 * @method int getQuoteItemId()
 * @method Magento_Sales_Model_Order_Item setQuoteItemId(int $value)
 * @method int getStoreId()
 * @method Magento_Sales_Model_Order_Item setStoreId(int $value)
 * @method string getCreatedAt()
 * @method Magento_Sales_Model_Order_Item setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Magento_Sales_Model_Order_Item setUpdatedAt(string $value)
 * @method int getProductId()
 * @method Magento_Sales_Model_Order_Item setProductId(int $value)
 * @method string getProductType()
 * @method Magento_Sales_Model_Order_Item setProductType(string $value)
 * @method float getWeight()
 * @method Magento_Sales_Model_Order_Item setWeight(float $value)
 * @method int getIsVirtual()
 * @method Magento_Sales_Model_Order_Item setIsVirtual(int $value)
 * @method string getSku()
 * @method Magento_Sales_Model_Order_Item setSku(string $value)
 * @method string getName()
 * @method Magento_Sales_Model_Order_Item setName(string $value)
 * @method string getDescription()
 * @method Magento_Sales_Model_Order_Item setDescription(string $value)
 * @method string getAppliedRuleIds()
 * @method Magento_Sales_Model_Order_Item setAppliedRuleIds(string $value)
 * @method string getAdditionalData()
 * @method Magento_Sales_Model_Order_Item setAdditionalData(string $value)
 * @method int getFreeShipping()
 * @method Magento_Sales_Model_Order_Item setFreeShipping(int $value)
 * @method int getIsQtyDecimal()
 * @method Magento_Sales_Model_Order_Item setIsQtyDecimal(int $value)
 * @method int getNoDiscount()
 * @method Magento_Sales_Model_Order_Item setNoDiscount(int $value)
 * @method float getQtyBackordered()
 * @method Magento_Sales_Model_Order_Item setQtyBackordered(float $value)
 * @method float getQtyCanceled()
 * @method Magento_Sales_Model_Order_Item setQtyCanceled(float $value)
 * @method float getQtyInvoiced()
 * @method Magento_Sales_Model_Order_Item setQtyInvoiced(float $value)
 * @method float getQtyOrdered()
 * @method Magento_Sales_Model_Order_Item setQtyOrdered(float $value)
 * @method float getQtyRefunded()
 * @method Magento_Sales_Model_Order_Item setQtyRefunded(float $value)
 * @method float getQtyShipped()
 * @method Magento_Sales_Model_Order_Item setQtyShipped(float $value)
 * @method float getBaseCost()
 * @method Magento_Sales_Model_Order_Item setBaseCost(float $value)
 * @method float getPrice()
 * @method Magento_Sales_Model_Order_Item setPrice(float $value)
 * @method float getBasePrice()
 * @method Magento_Sales_Model_Order_Item setBasePrice(float $value)
 * @method Magento_Sales_Model_Order_Item setOriginalPrice(float $value)
 * @method float getBaseOriginalPrice()
 * @method Magento_Sales_Model_Order_Item setBaseOriginalPrice(float $value)
 * @method float getTaxPercent()
 * @method Magento_Sales_Model_Order_Item setTaxPercent(float $value)
 * @method float getTaxAmount()
 * @method Magento_Sales_Model_Order_Item setTaxAmount(float $value)
 * @method float getBaseTaxAmount()
 * @method Magento_Sales_Model_Order_Item setBaseTaxAmount(float $value)
 * @method float getTaxInvoiced()
 * @method Magento_Sales_Model_Order_Item setTaxInvoiced(float $value)
 * @method float getBaseTaxInvoiced()
 * @method Magento_Sales_Model_Order_Item setBaseTaxInvoiced(float $value)
 * @method float getDiscountPercent()
 * @method Magento_Sales_Model_Order_Item setDiscountPercent(float $value)
 * @method float getDiscountAmount()
 * @method Magento_Sales_Model_Order_Item setDiscountAmount(float $value)
 * @method float getBaseDiscountAmount()
 * @method Magento_Sales_Model_Order_Item setBaseDiscountAmount(float $value)
 * @method float getDiscountInvoiced()
 * @method Magento_Sales_Model_Order_Item setDiscountInvoiced(float $value)
 * @method float getBaseDiscountInvoiced()
 * @method Magento_Sales_Model_Order_Item setBaseDiscountInvoiced(float $value)
 * @method float getAmountRefunded()
 * @method Magento_Sales_Model_Order_Item setAmountRefunded(float $value)
 * @method float getBaseAmountRefunded()
 * @method Magento_Sales_Model_Order_Item setBaseAmountRefunded(float $value)
 * @method float getRowTotal()
 * @method Magento_Sales_Model_Order_Item setRowTotal(float $value)
 * @method float getBaseRowTotal()
 * @method Magento_Sales_Model_Order_Item setBaseRowTotal(float $value)
 * @method float getRowInvoiced()
 * @method Magento_Sales_Model_Order_Item setRowInvoiced(float $value)
 * @method float getBaseRowInvoiced()
 * @method Magento_Sales_Model_Order_Item setBaseRowInvoiced(float $value)
 * @method float getRowWeight()
 * @method Magento_Sales_Model_Order_Item setRowWeight(float $value)
 * @method int getGiftMessageId()
 * @method Magento_Sales_Model_Order_Item setGiftMessageId(int $value)
 * @method int getGiftMessageAvailable()
 * @method Magento_Sales_Model_Order_Item setGiftMessageAvailable(int $value)
 * @method float getBaseTaxBeforeDiscount()
 * @method Magento_Sales_Model_Order_Item setBaseTaxBeforeDiscount(float $value)
 * @method float getTaxBeforeDiscount()
 * @method Magento_Sales_Model_Order_Item setTaxBeforeDiscount(float $value)
 * @method string getExtOrderItemId()
 * @method Magento_Sales_Model_Order_Item setExtOrderItemId(string $value)
 * @method string getWeeeTaxApplied()
 * @method Magento_Sales_Model_Order_Item setWeeeTaxApplied(string $value)
 * @method float getWeeeTaxAppliedAmount()
 * @method Magento_Sales_Model_Order_Item setWeeeTaxAppliedAmount(float $value)
 * @method float getWeeeTaxAppliedRowAmount()
 * @method Magento_Sales_Model_Order_Item setWeeeTaxAppliedRowAmount(float $value)
 * @method float getBaseWeeeTaxAppliedAmount()
 * @method Magento_Sales_Model_Order_Item setBaseWeeeTaxAppliedAmount(float $value)
 * @method float getBaseWeeeTaxAppliedRowAmnt()
 * @method Magento_Sales_Model_Order_Item setBaseWeeeTaxAppliedRowAmnt(float $value)
 * @method float getWeeeTaxDisposition()
 * @method Magento_Sales_Model_Order_Item setWeeeTaxDisposition(float $value)
 * @method float getWeeeTaxRowDisposition()
 * @method Magento_Sales_Model_Order_Item setWeeeTaxRowDisposition(float $value)
 * @method float getBaseWeeeTaxDisposition()
 * @method Magento_Sales_Model_Order_Item setBaseWeeeTaxDisposition(float $value)
 * @method float getBaseWeeeTaxRowDisposition()
 * @method Magento_Sales_Model_Order_Item setBaseWeeeTaxRowDisposition(float $value)
 * @method int getLockedDoInvoice()
 * @method Magento_Sales_Model_Order_Item setLockedDoInvoice(int $value)
 * @method int getLockedDoShip()
 * @method Magento_Sales_Model_Order_Item setLockedDoShip(int $value)
 * @method float getPriceInclTax()
 * @method Magento_Sales_Model_Order_Item setPriceInclTax(float $value)
 * @method float getBasePriceInclTax()
 * @method Magento_Sales_Model_Order_Item setBasePriceInclTax(float $value)
 * @method float getRowTotalInclTax()
 * @method Magento_Sales_Model_Order_Item setRowTotalInclTax(float $value)
 * @method float getBaseRowTotalInclTax()
 * @method Magento_Sales_Model_Order_Item setBaseRowTotalInclTax(float $value)
 * @method float getHiddenTaxAmount()
 * @method Magento_Sales_Model_Order_Item setHiddenTaxAmount(float $value)
 * @method float getBaseHiddenTaxAmount()
 * @method Magento_Sales_Model_Order_Item setBaseHiddenTaxAmount(float $value)
 * @method float getHiddenTaxInvoiced()
 * @method Magento_Sales_Model_Order_Item setHiddenTaxInvoiced(float $value)
 * @method float getBaseHiddenTaxInvoiced()
 * @method Magento_Sales_Model_Order_Item setBaseHiddenTaxInvoiced(float $value)
 * @method float getHiddenTaxRefunded()
 * @method Magento_Sales_Model_Order_Item setHiddenTaxRefunded(float $value)
 * @method float getBaseHiddenTaxRefunded()
 * @method Magento_Sales_Model_Order_Item setBaseHiddenTaxRefunded(float $value)
 * @method int getIsNominal()
 * @method Magento_Sales_Model_Order_Item setIsNominal(int $value)
 * @method float getTaxCanceled()
 * @method Magento_Sales_Model_Order_Item setTaxCanceled(float $value)
 * @method float getHiddenTaxCanceled()
 * @method Magento_Sales_Model_Order_Item setHiddenTaxCanceled(float $value)
 * @method float getTaxRefunded()
 * @method Magento_Sales_Model_Order_Item setTaxRefunded(float $value)
 * @method float getBaseTaxRefunded()
 * @method Magento_Sales_Model_Order_Item setBaseTaxRefunded(float $value)
 * @method float getDiscountRefunded()
 * @method Magento_Sales_Model_Order_Item setDiscountRefunded(float $value)
 * @method float getBaseDiscountRefunded()
 * @method Magento_Sales_Model_Order_Item setBaseDiscountRefunded(float $value)
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Order_Item extends Magento_Core_Model_Abstract
{

    const STATUS_PENDING        = 1; // No items shipped, invoiced, canceled, refunded nor backordered
    const STATUS_SHIPPED        = 2; // When qty ordered - [qty canceled + qty returned] = qty shipped
    const STATUS_INVOICED       = 9; // When qty ordered - [qty canceled + qty returned] = qty invoiced
    const STATUS_BACKORDERED    = 3; // When qty ordered - [qty canceled + qty returned] = qty backordered
    const STATUS_CANCELED       = 5; // When qty ordered = qty canceled
    const STATUS_PARTIAL        = 6; // If [qty shipped or(max of two) qty invoiced + qty canceled + qty returned]
                                     // < qty ordered
    const STATUS_MIXED          = 7; // All other combinations
    const STATUS_REFUNDED       = 8; // When qty ordered = qty refunded

    const STATUS_RETURNED       = 4; // When qty ordered = qty returned // not used at the moment

    protected $_eventPrefix = 'sales_order_item';
    protected $_eventObject = 'item';

    protected static $_statuses = null;

    /**
     * Order instance
     *
     * @var Magento_Sales_Model_Order
     */
    protected $_order       = null;
    protected $_parentItem  = null;
    protected $_children    = array();

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Resource_Order_Item');
    }

    /**
     * Prepare data before save
     *
     * @return Magento_Sales_Model_Order_Item
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->getOrderId() && $this->getOrder()) {
            $this->setOrderId($this->getOrder()->getId());
        }
        if ($this->getParentItem()) {
            $this->setParentItemId($this->getParentItem()->getId());
        }
        return $this;
    }

    /**
     * Set parent item
     *
     * @param   Magento_Sales_Model_Order_Item $item
     * @return  Magento_Sales_Model_Order_Item
     */
    public function setParentItem($item)
    {
        if ($item) {
            $this->_parentItem = $item;
            $item->setHasChildren(true);
            $item->addChildItem($this);
        }
        return $this;
    }

    /**
     * Get parent item
     *
     * @return Magento_Sales_Model_Order_Item || null
     */
    public function getParentItem()
    {
        return $this->_parentItem;
    }

    /**
     * Check item invoice availability
     *
     * @return bool
     */
    public function canInvoice()
    {
        return $this->getQtyToInvoice()>0;
    }

    /**
     * Check item ship availability
     *
     * @return bool
     */
    public function canShip()
    {
        return $this->getQtyToShip()>0;
    }

    /**
     * Check item refund availability
     *
     * @return bool
     */
    public function canRefund()
    {
        return $this->getQtyToRefund()>0;
    }

    /**
     * Retrieve item qty available for ship
     *
     * @return float|integer
     */
    public function getQtyToShip()
    {
        if ($this->isDummy(true)) {
            return 0;
        }

        return $this->getSimpleQtyToShip();
    }

    /**
     * Retrieve item qty available for ship
     *
     * @return float|integer
     */
    public function getSimpleQtyToShip()
    {
        $qty = $this->getQtyOrdered()
            - $this->getQtyShipped()
            - $this->getQtyRefunded()
            - $this->getQtyCanceled();
        return max($qty, 0);
    }

    /**
     * Retrieve item qty available for invoice
     *
     * @return float|integer
     */
    public function getQtyToInvoice()
    {
        if ($this->isDummy()) {
            return 0;
        }

        $qty = $this->getQtyOrdered()
            - $this->getQtyInvoiced()
            - $this->getQtyCanceled();
        return max($qty, 0);
    }

    /**
     * Retrieve item qty available for refund
     *
     * @return float|integer
     */
    public function getQtyToRefund()
    {
        if ($this->isDummy()) {
            return 0;
        }

        return max($this->getQtyInvoiced()-$this->getQtyRefunded(), 0);
    }

    /**
     * Retrieve item qty available for cancel
     *
     * @return float|integer
     */
    public function getQtyToCancel()
    {
        $qtyToCancel = min($this->getQtyToInvoice(), $this->getQtyToShip());
        return max($qtyToCancel, 0);
    }

    /**
     * Declare order
     *
     * @param   Magento_Sales_Model_Order $order
     * @return  Magento_Sales_Model_Order_Item
     */
    public function setOrder(Magento_Sales_Model_Order $order)
    {
        $this->_order = $order;
        $this->setOrderId($order->getId());
        return $this;
    }

    /**
     * Retrieve order model object
     *
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        if (is_null($this->_order) && ($orderId = $this->getOrderId())) {
            $order = Mage::getModel('Magento_Sales_Model_Order');
            $order->load($orderId);
            $this->setOrder($order);
        }
        return $this->_order;
    }

    /**
     * Retrieve item status identifier
     *
     * @return int
     */
    public function getStatusId()
    {
        $backordered = (float)$this->getQtyBackordered();
        if (!$backordered && $this->getHasChildren()) {
            $backordered = (float)$this->_getQtyChildrenBackordered();
        }
        $canceled    = (float)$this->getQtyCanceled();
        $invoiced    = (float)$this->getQtyInvoiced();
        $ordered     = (float)$this->getQtyOrdered();
        $refunded    = (float)$this->getQtyRefunded();
        $shipped     = (float)$this->getQtyShipped();

        $actuallyOrdered = $ordered - $canceled - $refunded;

        if (!$invoiced && !$shipped && !$refunded && !$canceled && !$backordered) {
            return self::STATUS_PENDING;
        }
        if ($shipped && $invoiced && ($actuallyOrdered == $shipped)) {
            return self::STATUS_SHIPPED;
        }

        if ($invoiced && !$shipped && ($actuallyOrdered == $invoiced)) {
            return self::STATUS_INVOICED;
        }

        if ($backordered && ($actuallyOrdered == $backordered) ) {
            return self::STATUS_BACKORDERED;
        }

        if ($refunded && $ordered == $refunded) {
            return self::STATUS_REFUNDED;
        }

        if ($canceled && $ordered == $canceled) {
            return self::STATUS_CANCELED;
        }

        if (max($shipped, $invoiced) < $actuallyOrdered) {
            return self::STATUS_PARTIAL;
        }

        return self::STATUS_MIXED;
    }

    /**
     * Retrieve backordered qty of children items
     *
     * @return float|null
     */
    protected function _getQtyChildrenBackordered()
    {
        $backordered = null;
        foreach ($this->_children as $childItem) {
            $backordered += (float)$childItem->getQtyBackordered();
        }

        return $backordered;
    }

    /**
     * Retrieve status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->getStatusName($this->getStatusId());
    }

    /**
     * Retrieve status name
     *
     * @return string
     */
    public static function getStatusName($statusId)
    {
        if (is_null(self::$_statuses)) {
            self::getStatuses();
        }
        if (isset(self::$_statuses[$statusId])) {
            return self::$_statuses[$statusId];
        }
        return __('Unknown Status');
    }

    /**
     * Cancel order item
     *
     * @return Magento_Sales_Model_Order_Item
     */
    public function cancel()
    {
        if ($this->getStatusId() !== self::STATUS_CANCELED) {
            $this->_eventManager->dispatch('sales_order_item_cancel', array('item'=>$this));
            $this->setQtyCanceled($this->getQtyToCancel());
            $this->setTaxCanceled($this->getTaxCanceled() + $this->getBaseTaxAmount() * $this->getQtyCanceled() / $this->getQtyOrdered());
            $this->setHiddenTaxCanceled($this->getHiddenTaxCanceled() + $this->getHiddenTaxAmount() * $this->getQtyCanceled() / $this->getQtyOrdered());
        }
        return $this;
    }

    /**
     * Retrieve order item statuses array
     *
     * @return array
     */
    public static function getStatuses()
    {
        if (is_null(self::$_statuses)) {
            self::$_statuses = array(
                //self::STATUS_PENDING        => __('Pending'),
                self::STATUS_PENDING        => __('Ordered'),
                self::STATUS_SHIPPED        => __('Shipped'),
                self::STATUS_INVOICED       => __('Invoiced'),
                self::STATUS_BACKORDERED    => __('Backordered'),
                self::STATUS_RETURNED       => __('Returned'),
                self::STATUS_REFUNDED       => __('Refunded'),
                self::STATUS_CANCELED       => __('Canceled'),
                self::STATUS_PARTIAL        => __('Partial'),
                self::STATUS_MIXED          => __('Mixed'),
            );
        }
        return self::$_statuses;
    }

    /**
     * Redeclare getter for back compatibility
     *
     * @return float
     */
    public function getOriginalPrice()
    {
        $price = $this->getData('original_price');
        if (is_null($price)) {
            return $this->getPrice();
        }
        return $price;
    }

    /**
     * Set product options
     *
     * @param   array $options
     * @return  Magento_Sales_Model_Order_Item
     */
    public function setProductOptions(array $options)
    {
        $this->setData('product_options', serialize($options));
        return $this;
    }

    /**
     * Get product options array
     *
     * @return array
     */
    public function getProductOptions()
    {
        if ($options = $this->_getData('product_options')) {
            return unserialize($options);
        }
        return array();
    }

    /**
     * Get product options array by code.
     * If code is null return all options
     *
     * @param string $code
     * @return array
     */
    public function getProductOptionByCode($code=null)
    {
        $options = $this->getProductOptions();
        if (is_null($code)) {
            return $options;
        }
        if (isset($options[$code])) {
            return $options[$code];
        }
        return null;
    }

    /**
     * Return real product type of item or NULL if item is not composite
     *
     * @return string | null
     */
    public function getRealProductType()
    {
        if ($productType = $this->getProductOptionByCode('real_product_type')) {
            return $productType;
        }
        return null;
    }

    /**
     * Adds child item to this item
     *
     * @param Magento_Sales_Model_Order_Item $item
     */
    public function addChildItem($item)
    {
        if ($item instanceof Magento_Sales_Model_Order_Item) {
            $this->_children[] = $item;
        } else if (is_array($item)) {
            $this->_children = array_merge($this->_children, $item);
        }
    }

    /**
     * Return chilgren items of this item
     *
     * @return array
     */
    public function getChildrenItems() {
        return $this->_children;
    }

    /**
     * Return checking of what calculation
     * type was for this product
     *
     * @return bool
     */
    public function isChildrenCalculated() {
        if ($parentItem = $this->getParentItem()) {
            $options = $parentItem->getProductOptions();
        } else {
            $options = $this->getProductOptions();
        }

        if (isset($options['product_calculations']) &&
             $options['product_calculations'] == Magento_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD) {
                return true;
        }
        return false;
    }
    /**
     * Check if discount has to be applied to parent item
     *
     * @return bool
     */
    public function getForceApplyDiscountToParentItem()
    {
        if ($this->getParentItem()) {
            $product = $this->getParentItem()->getProduct();
        } else {
            $product = $this->getProduct();
        }

        return $product->getTypeInstance()->getForceApplyDiscountToParentItem();
    }

    /**
     * Return checking of what shipment
     * type was for this product
     *
     * @return bool
     */
    public function isShipSeparately() {
        if ($parentItem = $this->getParentItem()) {
            $options = $parentItem->getProductOptions();
        } else {
            $options = $this->getProductOptions();
        }

        if (isset($options['shipment_type']) &&
             $options['shipment_type'] == Magento_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY) {
                return true;
        }
        return false;
    }

    /**
     * This is Dummy item or not
     * if $shipment is true then we checking this for shipping situation if not
     * then we checking this for calculation
     *
     * @param bool $shipment
     * @return bool
     */
    public function isDummy($shipment = false){
        if ($shipment) {
            if ($this->getHasChildren() && $this->isShipSeparately()) {
                return true;
            }

            if ($this->getHasChildren() && !$this->isShipSeparately()) {
                return false;
            }

            if ($this->getParentItem() && $this->isShipSeparately()) {
                return false;
            }

            if ($this->getParentItem() && !$this->isShipSeparately()) {
                return true;
            }
        } else {
            if ($this->getHasChildren() && $this->isChildrenCalculated()) {
                return true;
            }

            if ($this->getHasChildren() && !$this->isChildrenCalculated()) {
                return false;
            }

            if ($this->getParentItem() && $this->isChildrenCalculated()) {
                return false;
            }

            if ($this->getParentItem() && !$this->isChildrenCalculated()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns formatted buy request - object, holding request received from
     * product view page with keys and options for configured product
     *
     * @return Magento_Object
     */
    public function getBuyRequest()
    {
        $option = $this->getProductOptionByCode('info_buyRequest');
        if (!$option) {
            $option = array();
        }
        $buyRequest = new Magento_Object($option);
        $buyRequest->setQty($this->getQtyOrdered() * 1);
        return $buyRequest;
    }

    /**
     * Retrieve product
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->getData('product')) {
            $product = Mage::getModel('Magento_Catalog_Model_Product')->load($this->getProductId());
            $this->setProduct($product);
        }

        return $this->getData('product');
    }
}
