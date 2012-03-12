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
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract API2 class for orders
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Sales_Model_Api2_Orders_Rest extends Mage_Sales_Model_Api2_Orders
{
    /**
     * Add gift message info to select
     *
     * @param Mage_Sales_Model_Resource_Order_Collection $collection
     * @return Mage_Sales_Model_Api2_Orders_Rest
     */
    protected function _addGiftMessageInfo(Mage_Sales_Model_Resource_Order_Collection $collection)
    {
        $collection->getSelect()->joinLeft(
            array('gift_message' => $collection->getTable('giftmessage/message')),
            'main_table.gift_message_id = gift_message.gift_message_id',
            array(
                'gift_message_from' => 'gift_message.sender',
                'gift_message_to'   => 'gift_message.recipient',
                'gift_message_body' => 'gift_message.message'
            )
        );

        return $this;
    }

    /**
     * Add order payment method field to select
     *
     * @param Mage_Sales_Model_Resource_Order_Collection $collection
     * @return Mage_Sales_Model_Api2_Orders_Rest
     */
    protected function _addPaymentMethodInfo(Mage_Sales_Model_Resource_Order_Collection $collection)
    {
        $collection->getSelect()->joinLeft(
            array('payment_method' => $collection->getTable('sales/order_payment')),
            'main_table.entity_id = payment_method.parent_id',
            array('payment_method' => 'payment_method.method')
        );

        return $this;
    }

    /**
     * Retrieve a list or orders' addresses in a form of [order ID => array of addresses, ...]
     *
     * @param array $orderIds Orders identifiers
     * @return array
     */
    protected function _getAddresses(array $orderIds)
    {
        $addresses = array();

        if ($this->_isSubCallAllowed('addresses')) {
            /** @var $addressesFilter Mage_Api2_Model_Acl_Filter */
            $addressesFilter = $this->_getSubModel('addresses', array())->getFilter();
            // do addresses request if at least one attribute allowed
            if ($addressesFilter->getAllowedAttributes()) {
                /* @var $collection Mage_Sales_Model_Resource_Order_Address_Collection */
                $collection = Mage::getResourceModel('sales/order_address_collection');

                $collection->addAttributeToFilter('parent_id', $orderIds);

                foreach ($collection->getItems() as $item) {
                    $addresses[$item->getParentId()][] = $addressesFilter->out($item->toArray());
                }
            }
        }
        return $addresses;
    }

    /**
     * Retrieve collection instance for orders
     *
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        /** @var $collection Mage_Sales_Model_Resource_Order_Collection */
        $collection = Mage::getResourceModel('sales/order_collection');

        $this->_applyCollectionModifiers($collection);

        return $collection;
    }

    /**
     * Retrieve a list or orders' comments in a form of [order ID => array of comments, ...]
     *
     * @param array $orderIds Orders identifiers
     * @return array
     */
    protected function _getComments(array $orderIds)
    {
        $comments = array();

        if ($this->_isOrderCommentsAllowed() && $this->_isSubCallAllowed('order_comments')) {
            /** @var $commentsFilter Mage_Api2_Model_Acl_Filter */
            $commentsFilter = $this->_getSubModel('order_comments', array())->getFilter();
            // do comments request if at least one attribute allowed
            if ($commentsFilter->getAllowedAttributes()) {
                /* @var $collection Mage_Sales_Model_Resource_Order_Status_History_Collection */
                $collection = Mage::getResourceModel('sales/order_status_history_collection');

                $collection->setOrderFilter($orderIds)
                    ->addFieldToFilter('entity_name', Mage_Sales_Model_Order::HISTORY_ENTITY_NAME);

                foreach ($collection->getItems() as $item) {
                    $comments[$item->getParentId()]['order_comments'][] = $commentsFilter->out($item->toArray());
                }
            }
        }
        return $comments;
    }

    /**
     * Retrieve a list or orders' items in a form of [order ID => array of items, ...]
     *
     * @param array $orderIds Orders identifiers
     * @return array
     */
    protected function _getItems(array $orderIds)
    {
        $items = array();

        if ($this->_isSubCallAllowed('order_items')) {
            /** @var $itemsFilter Mage_Api2_Model_Acl_Filter */
            $itemsFilter = $this->_getSubModel('order_items', array())->getFilter();
            // do items request if at least one attribute allowed
            if ($itemsFilter->getAllowedAttributes()) {
                /* @var $collection Mage_Sales_Model_Resource_Order_Item_Collection */
                $collection = Mage::getResourceModel('sales/order_item_collection');

                $collection->addAttributeToFilter('order_id', $orderIds);

                foreach ($collection->getItems() as $item) {
                    $items[$item->getOrderId()][] = $itemsFilter->out($item->toArray());
                }
            }
        }
        return $items;
    }

    /**
     * Get location for given resource
     *
     * @param Mage_Core_Model_Abstract $product
     * @return string Location of new resource
     */
    protected function _getLocation(Mage_Core_Model_Abstract $product)
    {
        return '/';
    }

    /**
     * Get orders list
     *
     * @return array
     */
    protected function _retrieve()
    {
        /** @var $collection Mage_Sales_Model_Resource_Order_Collection */
        $collection = $this->_getCollectionForRetrieve();

        if ($this->_isPaymentMethodAllowed()) {
            $this->_addPaymentMethodInfo($collection);
        }
        if ($this->_isGiftMessageAllowed()) {
            $this->_addGiftMessageInfo($collection);
        }
        $ordersData = array();

        foreach ($collection->getItems() as $order) {
            $ordersData[$order->getId()] = $order->toArray();
        }
        foreach ($this->_getAddresses(array_keys($ordersData)) as $orderId => $addresses) {
            $ordersData[$orderId]['addresses'] = $addresses;
        }
        foreach ($this->_getItems(array_keys($ordersData)) as $orderId => $items) {
            $ordersData[$orderId]['order_items'] = $items;
        }
        foreach ($this->_getComments(array_keys($ordersData)) as $orderId => $comments) {
            $ordersData[$orderId]['order_comments'] = $comments;
        }
        return $ordersData;
    }
}
