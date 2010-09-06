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
 * Enter description here ...
 *
 * @method Mage_Sales_Model_Resource_Order_Invoice_Item _getResource()
 * @method Mage_Sales_Model_Resource_Order_Invoice_Item getResource()
 * @method Mage_Sales_Model_Order_Invoice_Item getParentId()
 * @method int setParentId(int $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getBasePrice()
 * @method float setBasePrice(float $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getBaseWeeeTaxRowDisposition()
 * @method float setBaseWeeeTaxRowDisposition(float $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getWeeeTaxAppliedRowAmount()
 * @method float setWeeeTaxAppliedRowAmount(float $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getBaseWeeeTaxAppliedAmount()
 * @method float setBaseWeeeTaxAppliedAmount(float $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getTaxAmount()
 * @method float setTaxAmount(float $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getBaseRowTotal()
 * @method float setBaseRowTotal(float $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getDiscountAmount()
 * @method float setDiscountAmount(float $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getRowTotal()
 * @method float setRowTotal(float $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getWeeeTaxRowDisposition()
 * @method float setWeeeTaxRowDisposition(float $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getBaseDiscountAmount()
 * @method float setBaseDiscountAmount(float $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getBaseWeeeTaxDisposition()
 * @method float setBaseWeeeTaxDisposition(float $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getPriceInclTax()
 * @method float setPriceInclTax(float $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getWeeeTaxAppliedAmount()
 * @method float setWeeeTaxAppliedAmount(float $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getBaseTaxAmount()
 * @method float setBaseTaxAmount(float $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getBasePriceInclTax()
 * @method float setBasePriceInclTax(float $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getQty()
 * @method Mage_Sales_Model_Order_Invoice_Item getWeeeTaxDisposition()
 * @method float setWeeeTaxDisposition(float $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getBaseCost()
 * @method float setBaseCost(float $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getBaseWeeeTaxAppliedRowAmount()
 * @method float setBaseWeeeTaxAppliedRowAmount(float $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getPrice()
 * @method float setPrice(float $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getBaseRowTotalInclTax()
 * @method float setBaseRowTotalInclTax(float $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getRowTotalInclTax()
 * @method float setRowTotalInclTax(float $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getProductId()
 * @method int setProductId(int $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getOrderItemId()
 * @method int setOrderItemId(int $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getAdditionalData()
 * @method string setAdditionalData(string $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getDescription()
 * @method string setDescription(string $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getWeeeTaxApplied()
 * @method string setWeeeTaxApplied(string $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getSku()
 * @method string setSku(string $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getName()
 * @method string setName(string $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getHiddenTaxAmount()
 * @method float setHiddenTaxAmount(float $value)
 * @method Mage_Sales_Model_Order_Invoice_Item getBaseHiddenTaxAmount()
 * @method float setBaseHiddenTaxAmount(float $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Invoice_Item extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'sales_invoice_item';
    protected $_eventObject = 'invoice_item';

    protected $_invoice = null;
    protected $_orderItem = null;

    /**
     * Initialize resource model
     */
    function _construct()
    {
        $this->_init('sales/order_invoice_item');
    }

    /**
     * Declare invoice instance
     *
     * @param   Mage_Sales_Model_Order_Invoice $invoice
     * @return  Mage_Sales_Model_Order_Invoice_Item
     */
    public function setInvoice(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $this->_invoice = $invoice;
        return $this;
    }

    /**
     * Retrieve invoice instance
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function getInvoice()
    {
        return $this->_invoice;
    }

    /**
     * Declare order item instance
     *
     * @param   Mage_Sales_Model_Order_Item $item
     * @return  Mage_Sales_Model_Order_Invoice_Item
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
            if ($this->getInvoice()) {
                $this->_orderItem = $this->getInvoice()->getOrder()->getItemById($this->getOrderItemId());
            }
            else {
                $this->_orderItem = Mage::getModel('sales/order_item')
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
            $qty = (float) $qty;
        }
        else {
            $qty = (int) $qty;
        }
        $qty = $qty > 0 ? $qty : 0;
        /**
         * Check qty availability
         */
        $qtyToInvoice = sprintf("%F", $this->getOrderItem()->getQtyToInvoice());
        $qty = sprintf("%F", $qty);
        if ($qty <= $qtyToInvoice || $this->getOrderItem()->isDummy()) {
            $this->setData('qty', $qty);
        }
        else {
            Mage::throwException(
                Mage::helper('sales')->__('Invalid qty to invoice item "%s"', $this->getName())
            );
        }
        return $this;
    }

    /**
     * Applying qty to order item
     *
     * @return Mage_Sales_Model_Order_Invoice_Item
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
     * @return Mage_Sales_Model_Order_Invoice_Item
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
     * @return Mage_Sales_Model_Order_Invoice_Item
     */
    public function calcRowTotal()
    {
        $store          = $this->getInvoice()->getStore();
        $orderItem      = $this->getOrderItem();
        $orderItemQty   = $orderItem->getQtyOrdered();

        $rowTotal       = $orderItem->getRowTotal();
        $baseRowTotal   = $orderItem->getBaseRowTotal();
        $rowTotalInclTax    = $orderItem->getRowTotalInclTax();
        $baseRowTotalInclTax= $orderItem->getBaseRowTotalInclTax();

        $rowTotal       = $rowTotal/$orderItemQty*$this->getQty();
        $baseRowTotal   = $baseRowTotal/$orderItemQty*$this->getQty();

        $this->setRowTotal($store->roundPrice($rowTotal));
        $this->setBaseRowTotal($store->roundPrice($baseRowTotal));

        if ($rowTotalInclTax && $baseRowTotalInclTax) {
            $this->setRowTotalInclTax($store->roundPrice($rowTotalInclTax/$orderItemQty*$this->getQty()));
            $this->setBaseRowTotalInclTax($store->roundPrice($baseRowTotalInclTax/$orderItemQty*$this->getQty()));
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
     * @return Mage_Sales_Model_Order_Invoice_Item
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
     * @return Mage_Sales_Model_Order_Invoice_Item
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
