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


class Mage_Sales_Model_Order_Shipment extends Mage_Core_Model_Abstract
{
    const STATUS_NEW    = 1;

    protected $_items;
    protected $_tracks;
    protected $_order;

    /**
     * Initialize creditmemo resource model
     */
    protected function _construct()
    {
        $this->_init('sales/order_shipment');
    }

    /**
     * Declare order for creditmemo
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  Mage_Sales_Model_Order_Creditmemo
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        $this->setOrderId($order->getId())
            ->setStoreId($order->getStoreId());
        return $this;
    }

    /**
     * Retrieve the order the creditmemo for created for
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
     * Register invoice
     *
     * Apply to order, order items etc.
     *
     * @return unknown
     */
    public function register()
    {
        if ($this->getId()) {
            Mage::throwException(
                Mage::helper('sales')->__('Can not register existing shipment')
            );
        }

        $totalQty = 0;
        foreach ($this->getAllItems() as $item) {
            if ($item->getQty()>0) {
                $item->register();
                $totalQty+= $item->getQty();
            }
            else {
                $item->isDeleted(true);
            }
        }
        $this->setTotalQty($totalQty);

        return $this;
    }

    public function getItemsCollection()
    {
        if (empty($this->_items)) {
            $this->_items = Mage::getResourceModel('sales/order_shipment_item_collection');

            if ($this->getId()) {
                $this->_items
                    ->addAttributeToSelect('*')
                    ->setShipmentFilter($this->getId())
                    ->load();
                foreach ($this->_items as $item) {
                    $item->setShipment($this);
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

    public function addItem(Mage_Sales_Model_Order_Shipment_Item $item)
    {
        $item->setShipment($this)
            ->setParentId($this->getId());
        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }

    public function getTracksCollection()
    {
        if (empty($this->_tracks)) {
            $this->_tracks = Mage::getResourceModel('sales/order_shipment_track_collection');

            if ($this->getId()) {
                $this->_tracks
                    ->addAttributeToSelect('*')
                    ->setShipmentFilter($this->getId())
                    ->load();
                foreach ($this->_tracks as $track) {
                    $track->setShipment($this);
                }
            }
        }
        return $this->_tracks;
    }

    public function getAllTracks()
    {
        $tracks = array();
        foreach ($this->getTracksCollection() as $track) {
            if (!$track->isDeleted()) {
                $tracks[] =  $track;
            }
        }
        return $tracks;
    }

    public function getTrackById($trackId)
    {
        foreach ($this->getTracksCollection() as $track) {
            if ($track->getId()==$trackId) {
                return $track;
            }
        }
        return false;
    }

    public function addTrack(Mage_Sales_Model_Order_Shipment_Track $track)
    {
        $track->setShipment($this)
            ->setParentId($this->getId());
        if (!$track->getId()) {
            $this->getTracksCollection()->addItem($track);
        }
        return $this;
    }
}