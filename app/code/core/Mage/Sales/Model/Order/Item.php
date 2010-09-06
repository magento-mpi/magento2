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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order Item Model
 *
 * @method Mage_Sales_Model_Resource_Order_Item _getResource()
 * @method Mage_Sales_Model_Resource_Order_Item getResource()
 * @method Mage_Sales_Model_Order_Item getOrderId()
 * @method int setOrderId(int $value)
 * @method Mage_Sales_Model_Order_Item getParentItemId()
 * @method int setParentItemId(int $value)
 * @method Mage_Sales_Model_Order_Item getQuoteItemId()
 * @method int setQuoteItemId(int $value)
 * @method Mage_Sales_Model_Order_Item getStoreId()
 * @method int setStoreId(int $value)
 * @method Mage_Sales_Model_Order_Item getCreatedAt()
 * @method string setCreatedAt(string $value)
 * @method Mage_Sales_Model_Order_Item getUpdatedAt()
 * @method string setUpdatedAt(string $value)
 * @method Mage_Sales_Model_Order_Item getProductId()
 * @method int setProductId(int $value)
 * @method Mage_Sales_Model_Order_Item getProductType()
 * @method string setProductType(string $value)
 * @method Mage_Sales_Model_Order_Item getWeight()
 * @method float setWeight(float $value)
 * @method Mage_Sales_Model_Order_Item getIsVirtual()
 * @method int setIsVirtual(int $value)
 * @method Mage_Sales_Model_Order_Item getSku()
 * @method string setSku(string $value)
 * @method Mage_Sales_Model_Order_Item getName()
 * @method string setName(string $value)
 * @method Mage_Sales_Model_Order_Item getDescription()
 * @method string setDescription(string $value)
 * @method Mage_Sales_Model_Order_Item getAppliedRuleIds()
 * @method string setAppliedRuleIds(string $value)
 * @method Mage_Sales_Model_Order_Item getAdditionalData()
 * @method string setAdditionalData(string $value)
 * @method Mage_Sales_Model_Order_Item getFreeShipping()
 * @method int setFreeShipping(int $value)
 * @method Mage_Sales_Model_Order_Item getIsQtyDecimal()
 * @method int setIsQtyDecimal(int $value)
 * @method Mage_Sales_Model_Order_Item getNoDiscount()
 * @method int setNoDiscount(int $value)
 * @method Mage_Sales_Model_Order_Item getQtyBackordered()
 * @method float setQtyBackordered(float $value)
 * @method Mage_Sales_Model_Order_Item getQtyCanceled()
 * @method float setQtyCanceled(float $value)
 * @method Mage_Sales_Model_Order_Item getQtyInvoiced()
 * @method float setQtyInvoiced(float $value)
 * @method Mage_Sales_Model_Order_Item getQtyOrdered()
 * @method float setQtyOrdered(float $value)
 * @method Mage_Sales_Model_Order_Item getQtyRefunded()
 * @method float setQtyRefunded(float $value)
 * @method Mage_Sales_Model_Order_Item getQtyShipped()
 * @method float setQtyShipped(float $value)
 * @method Mage_Sales_Model_Order_Item getBaseCost()
 * @method float setBaseCost(float $value)
 * @method Mage_Sales_Model_Order_Item getPrice()
 * @method float setPrice(float $value)
 * @method Mage_Sales_Model_Order_Item getBasePrice()
 * @method float setBasePrice(float $value)
 * @method float setOriginalPrice(float $value)
 * @method Mage_Sales_Model_Order_Item getBaseOriginalPrice()
 * @method float setBaseOriginalPrice(float $value)
 * @method Mage_Sales_Model_Order_Item getTaxPercent()
 * @method float setTaxPercent(float $value)
 * @method Mage_Sales_Model_Order_Item getTaxAmount()
 * @method float setTaxAmount(float $value)
 * @method Mage_Sales_Model_Order_Item getBaseTaxAmount()
 * @method float setBaseTaxAmount(float $value)
 * @method Mage_Sales_Model_Order_Item getTaxInvoiced()
 * @method float setTaxInvoiced(float $value)
 * @method Mage_Sales_Model_Order_Item getBaseTaxInvoiced()
 * @method float setBaseTaxInvoiced(float $value)
 * @method Mage_Sales_Model_Order_Item getDiscountPercent()
 * @method float setDiscountPercent(float $value)
 * @method Mage_Sales_Model_Order_Item getDiscountAmount()
 * @method float setDiscountAmount(float $value)
 * @method Mage_Sales_Model_Order_Item getBaseDiscountAmount()
 * @method float setBaseDiscountAmount(float $value)
 * @method Mage_Sales_Model_Order_Item getDiscountInvoiced()
 * @method float setDiscountInvoiced(float $value)
 * @method Mage_Sales_Model_Order_Item getBaseDiscountInvoiced()
 * @method float setBaseDiscountInvoiced(float $value)
 * @method Mage_Sales_Model_Order_Item getAmountRefunded()
 * @method float setAmountRefunded(float $value)
 * @method Mage_Sales_Model_Order_Item getBaseAmountRefunded()
 * @method float setBaseAmountRefunded(float $value)
 * @method Mage_Sales_Model_Order_Item getRowTotal()
 * @method float setRowTotal(float $value)
 * @method Mage_Sales_Model_Order_Item getBaseRowTotal()
 * @method float setBaseRowTotal(float $value)
 * @method Mage_Sales_Model_Order_Item getRowInvoiced()
 * @method float setRowInvoiced(float $value)
 * @method Mage_Sales_Model_Order_Item getBaseRowInvoiced()
 * @method float setBaseRowInvoiced(float $value)
 * @method Mage_Sales_Model_Order_Item getRowWeight()
 * @method float setRowWeight(float $value)
 * @method Mage_Sales_Model_Order_Item getGiftMessageId()
 * @method int setGiftMessageId(int $value)
 * @method Mage_Sales_Model_Order_Item getGiftMessageAvailable()
 * @method int setGiftMessageAvailable(int $value)
 * @method Mage_Sales_Model_Order_Item getBaseTaxBeforeDiscount()
 * @method float setBaseTaxBeforeDiscount(float $value)
 * @method Mage_Sales_Model_Order_Item getTaxBeforeDiscount()
 * @method float setTaxBeforeDiscount(float $value)
 * @method Mage_Sales_Model_Order_Item getExtOrderItemId()
 * @method string setExtOrderItemId(string $value)
 * @method Mage_Sales_Model_Order_Item getWeeeTaxApplied()
 * @method string setWeeeTaxApplied(string $value)
 * @method Mage_Sales_Model_Order_Item getWeeeTaxAppliedAmount()
 * @method float setWeeeTaxAppliedAmount(float $value)
 * @method Mage_Sales_Model_Order_Item getWeeeTaxAppliedRowAmount()
 * @method float setWeeeTaxAppliedRowAmount(float $value)
 * @method Mage_Sales_Model_Order_Item getBaseWeeeTaxAppliedAmount()
 * @method float setBaseWeeeTaxAppliedAmount(float $value)
 * @method Mage_Sales_Model_Order_Item getBaseWeeeTaxAppliedRowAmount()
 * @method float setBaseWeeeTaxAppliedRowAmount(float $value)
 * @method Mage_Sales_Model_Order_Item getWeeeTaxDisposition()
 * @method float setWeeeTaxDisposition(float $value)
 * @method Mage_Sales_Model_Order_Item getWeeeTaxRowDisposition()
 * @method float setWeeeTaxRowDisposition(float $value)
 * @method Mage_Sales_Model_Order_Item getBaseWeeeTaxDisposition()
 * @method float setBaseWeeeTaxDisposition(float $value)
 * @method Mage_Sales_Model_Order_Item getBaseWeeeTaxRowDisposition()
 * @method float setBaseWeeeTaxRowDisposition(float $value)
 * @method Mage_Sales_Model_Order_Item getLockedDoInvoice()
 * @method int setLockedDoInvoice(int $value)
 * @method Mage_Sales_Model_Order_Item getLockedDoShip()
 * @method int setLockedDoShip(int $value)
 * @method Mage_Sales_Model_Order_Item getPriceInclTax()
 * @method float setPriceInclTax(float $value)
 * @method Mage_Sales_Model_Order_Item getBasePriceInclTax()
 * @method float setBasePriceInclTax(float $value)
 * @method Mage_Sales_Model_Order_Item getRowTotalInclTax()
 * @method float setRowTotalInclTax(float $value)
 * @method Mage_Sales_Model_Order_Item getBaseRowTotalInclTax()
 * @method float setBaseRowTotalInclTax(float $value)
 * @method Mage_Sales_Model_Order_Item getHiddenTaxAmount()
 * @method float setHiddenTaxAmount(float $value)
 * @method Mage_Sales_Model_Order_Item getBaseHiddenTaxAmount()
 * @method float setBaseHiddenTaxAmount(float $value)
 * @method Mage_Sales_Model_Order_Item getHiddenTaxInvoiced()
 * @method float setHiddenTaxInvoiced(float $value)
 * @method Mage_Sales_Model_Order_Item getBaseHiddenTaxInvoiced()
 * @method float setBaseHiddenTaxInvoiced(float $value)
 * @method Mage_Sales_Model_Order_Item getHiddenTaxRefunded()
 * @method float setHiddenTaxRefunded(float $value)
 * @method Mage_Sales_Model_Order_Item getBaseHiddenTaxRefunded()
 * @method float setBaseHiddenTaxRefunded(float $value)
 * @method Mage_Sales_Model_Order_Item getIsNominal()
 * @method int setIsNominal(int $value)
 * @method Mage_Sales_Model_Order_Item getTaxCanceled()
 * @method float setTaxCanceled(float $value)
 * @method Mage_Sales_Model_Order_Item getHiddenTaxCanceled()
 * @method float setHiddenTaxCanceled(float $value)
 * @method Mage_Sales_Model_Order_Item getTaxRefunded()
 * @method float setTaxRefunded(float $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Item extends Mage_Core_Model_Abstract
{

    const STATUS_PENDING        = 1; // No items shipped, invoiced, canceled, refunded nor backordered
    const STATUS_SHIPPED        = 2; // When qty ordered - [qty canceled + qty returned] = qty shipped
    const STATUS_INVOICED       = 9; // When qty ordered - [qty canceled + qty returned] = qty invoiced
    const STATUS_BACKORDERED    = 3; // When qty ordered - [qty canceled + qty returned] = qty backordered
    const STATUS_CANCELED       = 5; // When qty ordered = qty canceled
    const STATUS_PARTIAL        = 6; // If [qty shipped or(max of two) qty invoiced + qty canceled + qty returned] < qty ordered
    const STATUS_MIXED          = 7; // All other combinations
    const STATUS_REFUNDED       = 8; // When qty ordered = qty refunded

    const STATUS_RETURNED       = 4; // When qty ordered = qty returned // not used at the moment

    protected $_eventPrefix = 'sales_order_item';
    protected $_eventObject = 'item';

    protected static $_statuses = null;

    /**
     * Order instance
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_order       = null;
    protected $_parentItem  = null;
    protected $_children    = array();

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('sales/order_item');
    }

    /**
     * Prepare data before save
     *
     * @return Mage_Sales_Model_Order_Item
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
     * @param   Mage_Sales_Model_Order_Item $item
     * @return  Mage_Sales_Model_Order_Item
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
     * @return Mage_Sales_Model_Order_Item || null
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
     * @param   Mage_Sales_Model_Order $order
     * @return  Mage_Sales_Model_Order_Item
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        $this->setOrderId($order->getId());
        return $this;
    }

    /**
     * Retrieve order model object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (is_null($this->_order) && ($orderId = $this->getOrderId())) {
            $order = Mage::getModel('sales/order');
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
        $canceled    = (float)$this->getQtyCanceled();
        $invoiced    = (float)$this->getQtyInvoiced();
        $ordered     = (float)$this->getQtyOrdered();
        $refunded    = (float)$this->getQtyRefunded();
        $shipped     = (float)$this->getQtyShipped();

        $actuallyOrdered = $ordered - $canceled - $refunded;

        if (!$invoiced && !$shipped && !$refunded && !$canceled && !$backordered) {
            return self::STATUS_PENDING;
        }
        if ($shipped && !$invoiced && ($actuallyOrdered == $shipped)) {
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
        return Mage::helper('sales')->__('Unknown Status');
    }

    /**
     * Cancel order item
     *
     * @return Mage_Sales_Model_Order_Item
     */
    public function cancel()
    {
        if ($this->getStatusId() !== self::STATUS_CANCELED) {
            Mage::dispatchEvent('sales_order_item_cancel', array('item'=>$this));
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
                //self::STATUS_PENDING        => Mage::helper('sales')->__('Pending'),
                self::STATUS_PENDING        => Mage::helper('sales')->__('Ordered'),
                self::STATUS_SHIPPED        => Mage::helper('sales')->__('Shipped'),
                self::STATUS_INVOICED       => Mage::helper('sales')->__('Invoiced'),
                self::STATUS_BACKORDERED    => Mage::helper('sales')->__('Backordered'),
                self::STATUS_RETURNED       => Mage::helper('sales')->__('Returned'),
                self::STATUS_REFUNDED       => Mage::helper('sales')->__('Refunded'),
                self::STATUS_CANCELED       => Mage::helper('sales')->__('Canceled'),
                self::STATUS_PARTIAL        => Mage::helper('sales')->__('Partial'),
                self::STATUS_MIXED          => Mage::helper('sales')->__('Mixed'),
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
     * @return  Mage_Sales_Model_Order_Item
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
     * @param Mage_Sales_Model_Order_Item $item
     */
    public function addChildItem($item)
    {
        if ($item instanceof Mage_Sales_Model_Order_Item) {
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
             $options['product_calculations'] == Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD) {
                return true;
        }
        return false;
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
             $options['shipment_type'] == Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY) {
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
}
