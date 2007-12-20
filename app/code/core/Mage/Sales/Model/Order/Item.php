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


class Mage_Sales_Model_Order_Item extends Mage_Core_Model_Abstract
{

    const STATUS_PENDING        = 1; // No items backordered, shipped, or returned (may have canceled qty)
    const STATUS_SHIPPED        = 2; // When qty ordered - [qty canceled + qty returned] = qty shipped
    const STATUS_BACKORDERED    = 3; // When qty ordered - [qty canceled + qty returned] = qty backordered
    const STATUS_RETURNED       = 4; // When qty ordered = qty returned
    const STATUS_CANCELED       = 5; // When qty ordered = qty canceled
    const STATUS_PARTIAL        = 6; // If [qty shipped + qty canceled + qty returned] < qty ordered
    const STATUS_MIXED          = 7; // All other combinations

    protected static $_statuses = null;

    protected $_order;

    protected function _construct()
    {
        $this->_init('sales/order_item');
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
        return $this;
    }
    
    /**
     * Retrieve order model object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (is_null($this->_order) && ($orderId = $this->getParentId())) {
            $order = Mage::getModel('sales/order');
            $order->load($orderId);
            $this->setOrder($order);
        }
        return $this->_order;
    }
    
    /**
     * Import data from quote item
     *
     * @param   Mage_Sales_Model_Quote_Item $item
     * @return  Mage_Sales_Model_Order_Item
     */
    public function importQuoteItem(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $this->setQuoteItemId($item->getId())
            ->setStoreId($item->getQuote()->getStoreId())
            ->setProductId($item->getProductId())
            ->setSuperProductId($item->getSuperProductId())
            ->setParentProductId($item->getParentProductId())
            ->setSku($item->getSku())
            ->setName($item->getName())
            ->setDescription($item->getDescription())
            ->setWeight($item->getProduct()->getWeight())
            ->setQtyOrdered($item->getQty())
            ->setPrice($item->getCalculationPrice())
            ->setDiscountPercent($item->getDiscountPercent())
            ->setDiscountAmount($item->getDiscountAmount())
            ->setTaxPercent($item->getTaxPercent())
            ->setTaxAmount($item->getTaxAmount())
            ->setRowWeight($item->getRowWeight())
            ->setRowTotal($item->getRowTotal())
            ->setAppliedRuleIds($item->getAppliedRuleIds);
            
        Mage::dispatchEvent('sales_order_item_import_qoute_item', array('quote_item'=>$item, 'order_item'=>$this));
        return $this;
    }

    public function getStatusId()
    {
        if (!$this->getQtyBackordered() && !$this->getQtyShipped() && !$this->getQtyReturned() && !$this->getQtyCanceled()) {
            return self::STATUS_PENDING;
        } 
        elseif ($this->getQtyShipped() && ( $this->getQtyOrdered() - ($this->getQtyCanceled() + $this->getQtyReturned()) ) == $this->getQtyShipped() ) {
            return self::STATUS_SHIPPED;
        } 
        elseif ($this->getQtyBackordered() && ( $this->getQtyOrdered() - ($this->getQtyCanceled() + $this->getQtyReturned()) ) == $this->getQtyBackordered() ) {
            return self::STATUS_BACKORDERED;
        } 
        elseif ($this->getQtyReturned() && $this->getQtyOrdered() == $this->getQtyReturned() ) {
            return self::STATUS_RETURNED;
        } 
        elseif ($this->getQtyCanceled() && $this->getQtyOrdered() == $this->getQtyCanceled() ) {
            return self::STATUS_CANCELED;
        } 
        elseif ( ( $this->getQtyShipped() + $this->getQtyCanceled() + $this->getQtyReturned() ) < $this->getQtyOrdered() ) {
            return self::STATUS_PARTIAL;
        } 
        else {
            return self::STATUS_MIXED;
        }
    }

    public function getStatus()
    {
        return $this->getStatusName($this->getStatusId());
    }

    public static function getStatuses()
    {
        if (is_null(self::$_statuses)) {
            self::$_statuses = array(
                self::STATUS_PENDING        => Mage::helper('sales')->__('Pending'),
                self::STATUS_SHIPPED        => Mage::helper('sales')->__('Shipped'),
                self::STATUS_BACKORDERED    => Mage::helper('sales')->__('Backordered'),
                self::STATUS_RETURNED       => Mage::helper('sales')->__('Returned'),
                self::STATUS_CANCELED       => Mage::helper('sales')->__('Canceled'),
                self::STATUS_PARTIAL        => Mage::helper('sales')->__('Partial'),
                self::STATUS_MIXED          => Mage::helper('sales')->__('Mixed'),
            );
        }
        return self::$_statuses;
    }

    /**
     * Enter description here...
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
     * Enter description here...
     *
     * @return float|integer
     */
    public function getQtyToShip()
    {
        return max($this->getQtyOrdered() - $this->getQtyShipped() - $this->getQtyReturned() - $this->getQtyCanceled(), 0);
    }


    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Order_Item
     */
    public function cancel()
    {
        $this->setQtyCanceled($this->getQtyToShip());
        return $this;
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();
        Mage::dispatchEvent('sales_order_item_save_before', array('item'=>$this));
        return $this;
    }
}
