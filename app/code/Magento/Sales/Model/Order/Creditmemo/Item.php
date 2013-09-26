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
 * @method Magento_Sales_Model_Resource_Order_Creditmemo_Item _getResource()
 * @method Magento_Sales_Model_Resource_Order_Creditmemo_Item getResource()
 * @method int getParentId()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setParentId(int $value)
 * @method float getWeeeTaxAppliedRowAmount()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setWeeeTaxAppliedRowAmount(float $value)
 * @method float getBasePrice()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setBasePrice(float $value)
 * @method float getBaseWeeeTaxRowDisposition()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setBaseWeeeTaxRowDisposition(float $value)
 * @method float getTaxAmount()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setTaxAmount(float $value)
 * @method float getBaseWeeeTaxAppliedAmount()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setBaseWeeeTaxAppliedAmount(float $value)
 * @method float getWeeeTaxRowDisposition()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setWeeeTaxRowDisposition(float $value)
 * @method float getBaseRowTotal()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setBaseRowTotal(float $value)
 * @method float getDiscountAmount()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setDiscountAmount(float $value)
 * @method float getRowTotal()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setRowTotal(float $value)
 * @method float getWeeeTaxAppliedAmount()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setWeeeTaxAppliedAmount(float $value)
 * @method float getBaseDiscountAmount()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setBaseDiscountAmount(float $value)
 * @method float getBaseWeeeTaxDisposition()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setBaseWeeeTaxDisposition(float $value)
 * @method float getPriceInclTax()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setPriceInclTax(float $value)
 * @method float getBaseTaxAmount()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setBaseTaxAmount(float $value)
 * @method float getWeeeTaxDisposition()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setWeeeTaxDisposition(float $value)
 * @method float getBasePriceInclTax()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setBasePriceInclTax(float $value)
 * @method float getQty()
 * @method float getBaseCost()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setBaseCost(float $value)
 * @method float getBaseWeeeTaxAppliedRowAmnt()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setBaseWeeeTaxAppliedRowAmnt(float $value)
 * @method float getPrice()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setPrice(float $value)
 * @method float getBaseRowTotalInclTax()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setBaseRowTotalInclTax(float $value)
 * @method float getRowTotalInclTax()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setRowTotalInclTax(float $value)
 * @method int getProductId()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setProductId(int $value)
 * @method int getOrderItemId()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setOrderItemId(int $value)
 * @method string getAdditionalData()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setAdditionalData(string $value)
 * @method string getDescription()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setDescription(string $value)
 * @method string getWeeeTaxApplied()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setWeeeTaxApplied(string $value)
 * @method string getSku()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setSku(string $value)
 * @method string getName()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setName(string $value)
 * @method float getHiddenTaxAmount()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setHiddenTaxAmount(float $value)
 * @method float getBaseHiddenTaxAmount()
 * @method Magento_Sales_Model_Order_Creditmemo_Item setBaseHiddenTaxAmount(float $value)
 */
class Magento_Sales_Model_Order_Creditmemo_Item extends Magento_Core_Model_Abstract
{
    protected $_eventPrefix = 'sales_creditmemo_item';
    protected $_eventObject = 'creditmemo_item';
    protected $_creditmemo = null;
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
            $context, $registry, $resource, $resourceCollection, $data
        );
        $this->_orderItemFactory = $orderItemFactory;
    }

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Resource_Order_Creditmemo_Item');
    }

    /**
     * Declare creditmemo instance
     *
     * @param   Magento_Sales_Model_Order_Creditmemo $creditmemo
     * @return  Magento_Sales_Model_Order_Creditmemo_Item
     */
    public function setCreditmemo(Magento_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $this->_creditmemo = $creditmemo;
        return $this;
    }

    /**
     * Retrieve creditmemo instance
     *
     * @return Magento_Sales_Model_Order_Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->_creditmemo;
    }

    /**
     * Declare order item instance
     *
     * @param   Magento_Sales_Model_Order_Item $item
     * @return  Magento_Sales_Model_Order_Creditmemo_Item
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
        if (is_null($this->_orderItem)) {
            if ($this->getCreditmemo()) {
                $this->_orderItem = $this->getCreditmemo()->getOrder()->getItemById($this->getOrderItemId());
            } else {
                $this->_orderItem = $this->_orderItemFactory->create()->load($this->getOrderItemId());
            }
        }
        return $this->_orderItem;
    }

    /**
     * Declare qty
     *
     * @param   float $qty
     * @return  Magento_Sales_Model_Order_Creditmemo_Item
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
        if ($qty <= $this->getOrderItem()->getQtyToRefund() || $this->getOrderItem()->isDummy()) {
            $this->setData('qty', $qty);
        } else {
            throw new Magento_Core_Exception(
                __('We found an invalid quantity to refund item "%1".', $this->getName())
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
        $orderItem = $this->getOrderItem();

        $orderItem->setQtyRefunded($orderItem->getQtyRefunded() + $this->getQty());
        $orderItem->setTaxRefunded($orderItem->getTaxRefunded() + $this->getTaxAmount());
        $orderItem->setBaseTaxRefunded($orderItem->getBaseTaxRefunded() + $this->getBaseTaxAmount());
        $orderItem->setHiddenTaxRefunded($orderItem->getHiddenTaxRefunded() + $this->getHiddenTaxAmount());
        $orderItem->setBaseHiddenTaxRefunded($orderItem->getBaseHiddenTaxRefunded() + $this->getBaseHiddenTaxAmount());
        $orderItem->setAmountRefunded($orderItem->getAmountRefunded() + $this->getRowTotal());
        $orderItem->setBaseAmountRefunded($orderItem->getBaseAmountRefunded() + $this->getBaseRowTotal());
        $orderItem->setDiscountRefunded($orderItem->getDiscountRefunded() + $this->getDiscountAmount());
        $orderItem->setBaseDiscountRefunded($orderItem->getBaseDiscountRefunded() + $this->getBaseDiscountAmount());

        return $this;
    }

    public function cancel()
    {
        $this->getOrderItem()->setQtyRefunded(
            $this->getOrderItem()->getQtyRefunded()-$this->getQty()
        );
        $this->getOrderItem()->setTaxRefunded(
            $this->getOrderItem()->getTaxRefunded()
                - $this->getOrderItem()->getBaseTaxAmount() * $this->getQty() / $this->getOrderItem()->getQtyOrdered()
        );
        $this->getOrderItem()->setHiddenTaxRefunded(
            $this->getOrderItem()->getHiddenTaxRefunded()
                - $this->getOrderItem()->getHiddenTaxAmount() * $this->getQty() / $this->getOrderItem()->getQtyOrdered()
        );
        return $this;
    }

    /**
     * Invoice item row total calculation
     *
     * @return Magento_Sales_Model_Order_Invoice_Item
     */
    public function calcRowTotal()
    {
        $creditmemo           = $this->getCreditmemo();
        $orderItem            = $this->getOrderItem();
        $orderItemQtyInvoiced = $orderItem->getQtyInvoiced();

        $rowTotal            = $orderItem->getRowInvoiced() - $orderItem->getAmountRefunded();
        $baseRowTotal        = $orderItem->getBaseRowInvoiced() - $orderItem->getBaseAmountRefunded();
        $rowTotalInclTax     = $orderItem->getRowTotalInclTax();
        $baseRowTotalInclTax = $orderItem->getBaseRowTotalInclTax();

        if (!$this->isLast()) {
            $availableQty = $orderItemQtyInvoiced - $orderItem->getQtyRefunded();
            $rowTotal     = $creditmemo->roundPrice($rowTotal / $availableQty * $this->getQty());
            $baseRowTotal = $creditmemo->roundPrice($baseRowTotal / $availableQty * $this->getQty(), 'base');
        }
        $this->setRowTotal($rowTotal);
        $this->setBaseRowTotal($baseRowTotal);

        if ($rowTotalInclTax && $baseRowTotalInclTax) {
            $orderItemQty = $orderItem->getQtyOrdered();
            $this->setRowTotalInclTax(
                $creditmemo->roundPrice($rowTotalInclTax / $orderItemQty * $this->getQty(), 'including')
            );
            $this->setBaseRowTotalInclTax(
                $creditmemo->roundPrice($baseRowTotalInclTax / $orderItemQty * $this->getQty(), 'including_base')
            );
        }
        return $this;
    }

    /**
     * Checking if the item is last
     *
     * @return bool
     */
    public function isLast()
    {
        $orderItem = $this->getOrderItem();
        if ((string)(float)$this->getQty() == (string)(float)$orderItem->getQtyToRefund()
            && !$orderItem->getQtyToInvoice()
        ) {
            return true;
        }
        return false;
    }

    /**
     * Before object save
     *
     * @return Magento_Sales_Model_Order_Creditmemo_Item
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getCreditmemo()) {
            $this->setParentId($this->getCreditmemo()->getId());
        }

        return $this;
    }
}
