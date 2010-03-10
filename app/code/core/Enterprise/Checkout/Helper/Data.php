<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Enterprise Checkout Helper
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Helper_Data extends Enterprise_Enterprise_Helper_Core_Abstract
{
    /**
     * Return allowed product types that could be added to shopping cart
     *
     * @return array
     */
    public function getAvailableProductTypes() 
    {
        return Mage::getConfig()->getNode('adminhtml/sales/order/create/available_product_types')->asArray();
    }
    
    /**
     * Filter collection by removing not available product types
     *
     * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    public function applyProductTypesFilter($collection) 
    {
        $productTypes = array_keys($this->getAvailableProductTypes());
        foreach($collection->getItems() as $key => $item) {
            if ($item instanceof Mage_Catalog_Model_Product) {
                $type = $item->getTypeId();
            } else if ($item instanceof Mage_Sales_Model_Order_Item) {
                $type = $item->getProductType();
            } else if ($item instanceof Mage_Sales_Model_Quote_Item) {
                $type = $item->getProductType();
            } else {
                $type = '';
            }
            if (!in_array($type, $productTypes)) {
                $collection->removeItemByKey($key);
            }
        }
        return $collection;
    }
    
    /**
     * Return customer wishlist model
     *
     * @param Mage_Customer_Model_Customer|int $customer
     * @param Mage_Core_Model_Store $store
     * @return Mage_Wishlist_Model_Wishlist
     */
    public function getCustomerWishlist($customer, $store = null) 
    {
        $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customer, true);
        if ($store !== null) {
            if (!($store instanceof Mage_Core_Model_Store)) {
                $store = Mage::app()->getStore($store);
            }
            $wishlist->setStore($store)
                ->setSharedStoreIds($store->getWebsite()->getStoreIds());
        }
        return $wishlist;
    }
}
