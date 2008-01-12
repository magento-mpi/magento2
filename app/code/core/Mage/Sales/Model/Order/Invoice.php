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


class Mage_Sales_Model_Order_Invoice extends Mage_Core_Model_Abstract
{

    const STATUS_OPEN       = 1;
    const STATUS_PAYED      = 2;
    const STATUS_CANCELED   = 3;

    protected $_items;
    protected $_order;

    /**
     * Initialize invoice resource model
     */
    protected function _construct()
    {
        $this->_init('sales/order_invoice');
    }

    /**
     * Declare order for invoice
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  Mage_Sales_Model_Order_Invoice
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        $this->setOrderId($order->getId())
            ->setStoreId($order->getStoreId());
        return $this;
    }

    /**
     * Retrieve the order the invoice for created for
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order instanceof Mage_Sales_Model_Order) {
            $this->_order = Mage::getModel('sales/order')->load($this->getOrderId());
        }
        return $this->_order;
    }

    /**
     * Retrieve billing address
     *
     * @return Mage_Sales_Model_Order_Address
     */
    public function getBillingAddress()
    {
        return $this->getOrder()->getBillingAddress();
    }

    /**
     * Retrieve shipping address
     *
     * @return Mage_Sales_Model_Order_Address
     */
    public function getShippingAddress()
    {
        return $this->getOrder()->getShippingAddress();
    }

    /**
     * Capture invoice
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function capture()
    {
        return $this;
    }

    /**
     * Void invoice
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function void()
    {

        return $this;
    }


    public function collectTotals()
    {
        $amount = 0;
        foreach ($this->getAllItems() as $item) {
            $item->calcRowTotal();
            $amount+= $item->getRowTotal();
        }
        $this->setSubtotal($amount);
        $this->setGrandTotal($amount);
    }










/*********************** ITEMS ***************************/

    public function getItemsCollection()
    {
        if (empty($this->_items)) {
            $this->_items = Mage::getResourceModel('sales/order_invoice_item_collection');

            if ($this->getId()) {
                $this->_items
                    ->addAttributeToSelect('*')
                    ->setInvoiceFilter($this->getId())
                    ->load();
                foreach ($this->_items as $item) {
                    $item->setInvoice($this);
                }
            }
        }
        return $this->_items;
    }

    public function getAllItems()
    {
        $items = array();
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted()) {
                $items[] =  $item;
            }
        }
        return $items;
    }

    public function getItemById($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getId()==$itemId) {
                return $item;
            }
        }
        return false;
    }

    public function addItem(Mage_Sales_Model_Order_Invoice_Item $item)
    {
        $item->setInvoice($this)->setParentId($this->getId());
        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }

    public static function getStatuses()
    {
        if (is_null(self::$_statuses)) {
            self::$_statuses = array(
                self::STATUS_OPEN => Mage::helper('sales')->__('Pending'),
                self::STATUS_PAYED => Mage::helper('sales')->__('Payed'),
                self::STATUS_CANCELED => Mage::helper('sales')->__('Canceled'),
            );
        }
        return self::$_statuses;
    }

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
}