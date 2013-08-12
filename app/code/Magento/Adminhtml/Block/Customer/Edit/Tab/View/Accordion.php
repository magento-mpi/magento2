<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml customer recent orders grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Customer_Edit_Tab_View_Accordion extends Magento_Adminhtml_Block_Widget_Accordion
{
    protected function _prepareLayout()
    {
        $customer = Mage::registry('current_customer');

        $this->setId('customerViewAccordion');

        $this->addItem('lastOrders', array(
            'title'       => Mage::helper('Magento_Customer_Helper_Data')->__('Recent Orders'),
            'ajax'        => true,
            'content_url' => $this->getUrl('*/*/lastOrders', array('_current' => true)),
        ));

        // add shopping cart block of each website
        foreach (Mage::registry('current_customer')->getSharedWebsiteIds() as $websiteId) {
            $website = Mage::app()->getWebsite($websiteId);

            // count cart items
            $cartItemsCount = Mage::getModel('Mage_Sales_Model_Quote')
                ->setWebsite($website)->loadByCustomer($customer)
                ->getItemsCollection(false)
                ->addFieldToFilter('parent_item_id', array('null' => true))
                ->getSize();
            // prepare title for cart
            $title = Mage::helper('Magento_Customer_Helper_Data')->__('Shopping Cart - %d item(s)', $cartItemsCount);
            if (count($customer->getSharedWebsiteIds()) > 1) {
                $title = Mage::helper('Magento_Customer_Helper_Data')->__('Shopping Cart of %1$s - %2$d item(s)', $website->getName(), $cartItemsCount);
            }

            // add cart ajax accordion
            $this->addItem('shopingCart' . $websiteId, array(
                'title'   => $title,
                'ajax'    => true,
                'content_url' => $this->getUrl('*/*/viewCart', array('_current' => true, 'website_id' => $websiteId)),
            ));
        }

        // count wishlist items
        $wishlistCount = Mage::getModel('Magento_Wishlist_Model_Item')->getCollection()
            ->addCustomerIdFilter($customer->getId())
            ->addStoreData()
            ->getSize();
        // add wishlist ajax accordion
        $this->addItem('wishlist', array(
            'title' => Mage::helper('Magento_Customer_Helper_Data')->__('Wishlist - %d item(s)', $wishlistCount),
            'ajax'  => true,
            'content_url' => $this->getUrl('*/*/viewWishlist', array('_current' => true)),
        ));
    }
}
