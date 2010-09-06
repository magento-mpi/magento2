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
 * @method Mage_Sales_Model_Resource_Order_Creditmemo_Item _getResource()
 * @method Mage_Sales_Model_Resource_Order_Creditmemo_Item getResource()
 * @method Mage_Sales_Model_Order_Creditmemo_Item getParentId()
 * @method int setParentId(int $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getWeeeTaxAppliedRowAmount()
 * @method float setWeeeTaxAppliedRowAmount(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getBasePrice()
 * @method float setBasePrice(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getBaseWeeeTaxRowDisposition()
 * @method float setBaseWeeeTaxRowDisposition(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getTaxAmount()
 * @method float setTaxAmount(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getBaseWeeeTaxAppliedAmount()
 * @method float setBaseWeeeTaxAppliedAmount(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getWeeeTaxRowDisposition()
 * @method float setWeeeTaxRowDisposition(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getBaseRowTotal()
 * @method float setBaseRowTotal(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getDiscountAmount()
 * @method float setDiscountAmount(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getRowTotal()
 * @method float setRowTotal(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getWeeeTaxAppliedAmount()
 * @method float setWeeeTaxAppliedAmount(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getBaseDiscountAmount()
 * @method float setBaseDiscountAmount(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getBaseWeeeTaxDisposition()
 * @method float setBaseWeeeTaxDisposition(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getPriceInclTax()
 * @method float setPriceInclTax(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getBaseTaxAmount()
 * @method float setBaseTaxAmount(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getWeeeTaxDisposition()
 * @method float setWeeeTaxDisposition(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getBasePriceInclTax()
 * @method float setBasePriceInclTax(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getQty()
 * @method Mage_Sales_Model_Order_Creditmemo_Item getBaseCost()
 * @method float setBaseCost(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getBaseWeeeTaxAppliedRowAmount()
 * @method float setBaseWeeeTaxAppliedRowAmount(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getPrice()
 * @method float setPrice(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getBaseRowTotalInclTax()
 * @method float setBaseRowTotalInclTax(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getRowTotalInclTax()
 * @method float setRowTotalInclTax(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getProductId()
 * @method int setProductId(int $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getOrderItemId()
 * @method int setOrderItemId(int $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getAdditionalData()
 * @method string setAdditionalData(string $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getDescription()
 * @method string setDescription(string $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getWeeeTaxApplied()
 * @method string setWeeeTaxApplied(string $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getSku()
 * @method string setSku(string $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getName()
 * @method string setName(string $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getHiddenTaxAmount()
 * @method float setHiddenTaxAmount(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo_Item getBaseHiddenTaxAmount()
 * @method float setBaseHiddenTaxAmount(float $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Creditmemo_Item extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'sales_creditmemo_item';
    protected $_eventObject = 'creditmemo_item';
    protected $_creditmemo = null;
    protected $_orderItem = null;

    /**
     * Initialize resource model
     */
    function _construct()
    {
        $this->_init('sales/order_creditmemo_item');
    }

    /**
     * Declare creditmemo instance
     *
     * @param   Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return  Mage_Sales_Model_Order_Creditmemo_Item
     */
    public function setCreditmemo(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $this->_creditmemo = $creditmemo;
        return $this;
    }

    /**
     * Retrieve creditmemo instance
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->_creditmemo;
    }

    /**
     * Declare order item instance
     *
     * @param   Mage_Sales_Model_Order_Item $item
     * @return  Mage_Sales_Model_Order_Creditmemo_Item
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
            if ($this->getCreditmemo()) {
                $this->_orderItem = $this->getCreditmemo()->getOrder()->getItemById($this->getOrderItemId());
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
     * @return  Mage_Sales_Model_Order_Creditmemo_Item
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
        if ($qty <= $this->getOrderItem()->getQtyToRefund() || $this->getOrderItem()->isDummy()) {
            $this->setData('qty', $qty);
        }
        else {
            Mage::throwException(
                Mage::helper('sales')->__('Invalid qty to refund item "%s"', $this->getName())
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
        $this->getOrderItem()->setQtyRefunded(
            $this->getOrderItem()->getQtyRefunded() + $this->getQty()
        );
        $this->getOrderItem()->setTaxRefunded(
            $this->getOrderItem()->getTaxRefunded()
                + $this->getOrderItem()->getBaseTaxAmount() * $this->getQty() / $this->getOrderItem()->getQtyOrdered()
        );
        $this->getOrderItem()->setHiddenTaxRefunded(
            $this->getOrderItem()->getHiddenTaxRefunded()
                + $this->getOrderItem()->getHiddenTaxAmount() * $this->getQty() / $this->getOrderItem()->getQtyOrdered()
        );
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
     * @return Mage_Sales_Model_Order_Invoice_Item
     */
    public function calcRowTotal()
    {
        $store          = $this->getCreditmemo()->getStore();
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
        $orderItem = $this->getOrderItem();
        if ((string)(float)$this->getQty() == (string)(float)$orderItem->getQtyToRefund() && !$orderItem->getQtyToInvoice()) {
            return true;
        }
        return false;
    }

    /**
     * Before object save
     *
     * @return Mage_Sales_Model_Order_Creditmemo_Item
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
