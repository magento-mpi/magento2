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
 * @method Magento_Sales_Model_Resource_Order_Invoice_Item _getResource()
 * @method Magento_Sales_Model_Resource_Order_Invoice_Item getResource()
 * @method int getParentId()
 * @method Magento_Sales_Model_Order_Invoice_Item setParentId(int $value)
 * @method float getBasePrice()
 * @method Magento_Sales_Model_Order_Invoice_Item setBasePrice(float $value)
 * @method float getBaseWeeeTaxRowDisposition()
 * @method Magento_Sales_Model_Order_Invoice_Item setBaseWeeeTaxRowDisposition(float $value)
 * @method float getWeeeTaxAppliedRowAmount()
 * @method Magento_Sales_Model_Order_Invoice_Item setWeeeTaxAppliedRowAmount(float $value)
 * @method float getBaseWeeeTaxAppliedAmount()
 * @method Magento_Sales_Model_Order_Invoice_Item setBaseWeeeTaxAppliedAmount(float $value)
 * @method float getTaxAmount()
 * @method Magento_Sales_Model_Order_Invoice_Item setTaxAmount(float $value)
 * @method float getBaseRowTotal()
 * @method Magento_Sales_Model_Order_Invoice_Item setBaseRowTotal(float $value)
 * @method float getDiscountAmount()
 * @method Magento_Sales_Model_Order_Invoice_Item setDiscountAmount(float $value)
 * @method float getRowTotal()
 * @method Magento_Sales_Model_Order_Invoice_Item setRowTotal(float $value)
 * @method float getWeeeTaxRowDisposition()
 * @method Magento_Sales_Model_Order_Invoice_Item setWeeeTaxRowDisposition(float $value)
 * @method float getBaseDiscountAmount()
 * @method Magento_Sales_Model_Order_Invoice_Item setBaseDiscountAmount(float $value)
 * @method float getBaseWeeeTaxDisposition()
 * @method Magento_Sales_Model_Order_Invoice_Item setBaseWeeeTaxDisposition(float $value)
 * @method float getPriceInclTax()
 * @method Magento_Sales_Model_Order_Invoice_Item setPriceInclTax(float $value)
 * @method float getWeeeTaxAppliedAmount()
 * @method Magento_Sales_Model_Order_Invoice_Item setWeeeTaxAppliedAmount(float $value)
 * @method float getBaseTaxAmount()
 * @method Magento_Sales_Model_Order_Invoice_Item setBaseTaxAmount(float $value)
 * @method float getBasePriceInclTax()
 * @method Magento_Sales_Model_Order_Invoice_Item setBasePriceInclTax(float $value)
 * @method float getQty()
 * @method float getWeeeTaxDisposition()
 * @method Magento_Sales_Model_Order_Invoice_Item setWeeeTaxDisposition(float $value)
 * @method float getBaseCost()
 * @method Magento_Sales_Model_Order_Invoice_Item setBaseCost(float $value)
 * @method float getBaseWeeeTaxAppliedRowAmnt()
 * @method Magento_Sales_Model_Order_Invoice_Item setBaseWeeeTaxAppliedRowAmnt(float $value)
 * @method float getPrice()
 * @method Magento_Sales_Model_Order_Invoice_Item setPrice(float $value)
 * @method float getBaseRowTotalInclTax()
 * @method Magento_Sales_Model_Order_Invoice_Item setBaseRowTotalInclTax(float $value)
 * @method float getRowTotalInclTax()
 * @method Magento_Sales_Model_Order_Invoice_Item setRowTotalInclTax(float $value)
 * @method int getProductId()
 * @method Magento_Sales_Model_Order_Invoice_Item setProductId(int $value)
 * @method int getOrderItemId()
 * @method Magento_Sales_Model_Order_Invoice_Item setOrderItemId(int $value)
 * @method string getAdditionalData()
 * @method Magento_Sales_Model_Order_Invoice_Item setAdditionalData(string $value)
 * @method string getDescription()
 * @method Magento_Sales_Model_Order_Invoice_Item setDescription(string $value)
 * @method string getWeeeTaxApplied()
 * @method Magento_Sales_Model_Order_Invoice_Item setWeeeTaxApplied(string $value)
 * @method string getSku()
 * @method Magento_Sales_Model_Order_Invoice_Item setSku(string $value)
 * @method string getName()
 * @method Magento_Sales_Model_Order_Invoice_Item setName(string $value)
 * @method float getHiddenTaxAmount()
 * @method Magento_Sales_Model_Order_Invoice_Item setHiddenTaxAmount(float $value)
 * @method float getBaseHiddenTaxAmount()
 * @method Magento_Sales_Model_Order_Invoice_Item setBaseHiddenTaxAmount(float $value)
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Order_Invoice_Item extends Magento_Core_Model_Abstract
{
    protected $_eventPrefix = 'sales_invoice_item';
    protected $_eventObject = 'invoice_item';

    protected $_invoice = null;
    protected $_orderItem = null;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Resource_Order_Invoice_Item');
    }

    /**
     * Declare invoice instance
     *
     * @param   Magento_Sales_Model_Order_Invoice $invoice
     * @return  Magento_Sales_Model_Order_Invoice_Item
     */
    public function setInvoice(Magento_Sales_Model_Order_Invoice $invoice)
    {
        $this->_invoice = $invoice;
        return $this;
    }

    /**
     * Retrieve invoice instance
     *
     * @return Magento_Sales_Model_Order_Invoice
     */
    public function getInvoice()
    {
        return $this->_invoice;
    }

    /**
     * Declare order item instance
     *
     * @param   Magento_Sales_Model_Order_Item $item
     * @return  Magento_Sales_Model_Order_Invoice_Item
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
            if ($this->getInvoice()) {
                $this->_orderItem = $this->getInvoice()->getOrder()->getItemById($this->getOrderItemId());
            } else {
                $this->_orderItem = Mage::getModel('Magento_Sales_Model_Order_Item')
                    ->load($this->getOrderItemId());
            }
        }
        return $this->_orderItem;
    }

    /**
     * Declare qty
     *
     * @param   float $qty
     * @return  Magento_Sales_Model_Order_Invoice_Item
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
        $qtyToInvoice = sprintf("%F", $this->getOrderItem()->getQtyToInvoice());
        $qty = sprintf("%F", $qty);
        if ($qty <= $qtyToInvoice || $this->getOrderItem()->isDummy()) {
            $this->setData('qty', $qty);
        } else {
            Mage::throwException(
                __('We found an invalid quantity to invoice item "%1".', $this->getName())
            );
        }
        return $this;
    }

    /**
     * Applying qty to order item
     *
     * @return Magento_Sales_Model_Order_Invoice_Item
     */
    public function register()
    {
        $orderItem = $this->getOrderItem();
        $orderItem->setQtyInvoiced($orderItem->getQtyInvoiced()+$this->getQty());

        $orderItem->setTaxInvoiced($orderItem->getTaxInvoiced()+$this->getTaxAmount());
        $orderItem->setBaseTaxInvoiced($orderItem->getBaseTaxInvoiced()+$this->getBaseTaxAmount());
        $orderItem->setHiddenTaxInvoiced($orderItem->getHiddenTaxInvoiced()+$this->getHiddenTaxAmount());
        $orderItem->setBaseHiddenTaxInvoiced($orderItem->getBaseHiddenTaxInvoiced()+$this->getBaseHiddenTaxAmount());

        $orderItem->setDiscountInvoiced($orderItem->getDiscountInvoiced()+$this->getDiscountAmount());
        $orderItem->setBaseDiscountInvoiced($orderItem->getBaseDiscountInvoiced()+$this->getBaseDiscountAmount());

        $orderItem->setRowInvoiced($orderItem->getRowInvoiced()+$this->getRowTotal());
        $orderItem->setBaseRowInvoiced($orderItem->getBaseRowInvoiced()+$this->getBaseRowTotal());
        return $this;
    }

    /**
     * Cancelling invoice item
     *
     * @return Magento_Sales_Model_Order_Invoice_Item
     */
    public function cancel()
    {
        $orderItem = $this->getOrderItem();
        $orderItem->setQtyInvoiced($orderItem->getQtyInvoiced()-$this->getQty());

        $orderItem->setTaxInvoiced($orderItem->getTaxInvoiced()-$this->getTaxAmount());
        $orderItem->setBaseTaxInvoiced($orderItem->getBaseTaxInvoiced()-$this->getBaseTaxAmount());
        $orderItem->setHiddenTaxInvoiced($orderItem->getHiddenTaxInvoiced()-$this->getHiddenTaxAmount());
        $orderItem->setBaseHiddenTaxInvoiced($orderItem->getBaseHiddenTaxInvoiced()-$this->getBaseHiddenTaxAmount());


        $orderItem->setDiscountInvoiced($orderItem->getDiscountInvoiced()-$this->getDiscountAmount());
        $orderItem->setBaseDiscountInvoiced($orderItem->getBaseDiscountInvoiced()-$this->getBaseDiscountAmount());

        $orderItem->setRowInvoiced($orderItem->getRowInvoiced()-$this->getRowTotal());
        $orderItem->setBaseRowInvoiced($orderItem->getBaseRowInvoiced()-$this->getBaseRowTotal());
        return $this;
    }

    /**
     * Invoice item row total calculation
     *
     * @return Magento_Sales_Model_Order_Invoice_Item
     */
    public function calcRowTotal()
    {
        $invoice        = $this->getInvoice();
        $orderItem      = $this->getOrderItem();
        $orderItemQty   = $orderItem->getQtyOrdered();

        $rowTotal            = $orderItem->getRowTotal() - $orderItem->getRowInvoiced();
        $baseRowTotal        = $orderItem->getBaseRowTotal() - $orderItem->getBaseRowInvoiced();
        $rowTotalInclTax     = $orderItem->getRowTotalInclTax();
        $baseRowTotalInclTax = $orderItem->getBaseRowTotalInclTax();

        if (!$this->isLast()) {
            $availableQty = $orderItemQty - $orderItem->getQtyInvoiced();
            $rowTotal = $invoice->roundPrice($rowTotal / $availableQty * $this->getQty());
            $baseRowTotal = $invoice->roundPrice($baseRowTotal / $availableQty * $this->getQty(), 'base');
        }

        $this->setRowTotal($rowTotal);
        $this->setBaseRowTotal($baseRowTotal);

        if ($rowTotalInclTax && $baseRowTotalInclTax) {
            $this->setRowTotalInclTax($invoice->roundPrice($rowTotalInclTax / $orderItemQty * $this->getQty(), 'including'));
            $this->setBaseRowTotalInclTax($invoice->roundPrice($baseRowTotalInclTax / $orderItemQty * $this->getQty(), 'including_base'));
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
        if ((string)(float)$this->getQty() == (string)(float)$this->getOrderItem()->getQtyToInvoice()) {
            return true;
        }
        return false;
    }

    /**
     * Before object save
     *
     * @return Magento_Sales_Model_Order_Invoice_Item
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getInvoice()) {
            $this->setParentId($this->getInvoice()->getId());
        }

        return $this;
    }

    /**
     * After object save
     *
     * @return Magento_Sales_Model_Order_Invoice_Item
     */
    protected function _afterSave()
    {
        if (null ==! $this->_orderItem) {
            $this->_orderItem->save();
        }

        parent::_afterSave();
        return $this;
    }
}
