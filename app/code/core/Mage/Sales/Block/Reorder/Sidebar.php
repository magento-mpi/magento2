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
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales order view block
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Reorder_Sidebar extends Mage_Core_Block_Template
{
    /**
     * Initialize sidebar
     *
     */
    public function __construct()
    {
        parent::__construct();

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->setTemplate('sales/order/history.phtml');

            $orders = Mage::getResourceModel('sales/order_collection')
                ->addAttributeToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
                ->addAttributeToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
                ->addAttributeToSort('created_at', 'desc')
                ->setPage(1,1);
            //TODO: add filter by current website

            $this->setOrders($orders);
        }
    }

    /**
     * Retrieve Last Order model
     *
     * @return Mage_Sales_Model_Order
     */
    public function getLastOrder()
    {
        foreach ($this->getOrders() as $order) {
            return $order;
        }
        return false;
    }

    /**
     * Retrieve random items collection from order
     *
     * @param Mage_Sales_Model_Order $order
     * @return Mage_Sales_Model_Entity_Order_Item_Collection
     */
    public function getRandomItemCollection(Mage_Sales_Model_Order $order, $limit = 5)
    {
        return $order->getItemsRandomCollection($limit);
    }

    /**
     * Retrieve is show item flag
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return bool
     */
    public function getIsShowItem(Mage_Sales_Model_Order_Item $item)
    {
        return $item->getProduct() && is_null($item->getParentItem());
    }

    /**
     * Retrieve item is salable flag
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return bool
     */
    public function getIsSalableItem(Mage_Sales_Model_Order_Item $item)
    {
        return $item->getProduct() && $item->getProduct()->isSalable();
    }

    /**
     * Retrieve Product URL by item
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return string
     */
    public function getItemProductUrl(Mage_Sales_Model_Order_Item $item)
    {
        if ($item->getProduct()) {
            return $item->getProduct()->getProductUrl();
        }
        return null;
    }

    /**
     * Retrieve Product name by item
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return string
     */
    public function getItemProductName(Mage_Sales_Model_Order_Item $item)
    {
        return $this->htmlEscape($item->getName());
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (Mage::helper('sales/reorder')->isAllow() && Mage::getSingleton('customer/session')->isLoggedIn()) {
            return parent::_toHtml();
        }
        return '';
    }
}