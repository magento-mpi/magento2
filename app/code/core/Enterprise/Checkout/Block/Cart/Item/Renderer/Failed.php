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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * SKU failed items renderer
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 */
class Enterprise_Checkout_Block_Cart_Item_Renderer_Failed extends Mage_Core_Block_Template
{
    /**
     * Item instance
     *
     * @var Varien_Object
     */
    protected $_item;

    /**
     * Product URL
     *
     * @var string
     */
    protected $_productUrl;

    /**
     * Helper instance
     *
     * @var Enterprise_Checkout_Helper_Data
     */
    protected $_helper;

    /**
     * Retrieve helper instance
     *
     * @return Enterprise_Checkout_Helper_Data
     */
    protected function _getHelper()
    {
        if (is_null($this->_helper)) {
            $this->_helper = Mage::helper('Enterprise_Checkout_Helper_Data');
        }
        return $this->_helper;
    }

    /**
     * Set renderer template
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('checkout/cart/item/failed.phtml');
    }

    /**
     * Set item for render
     *
     * @param   Varien_Object $item
     * @return  Enterprise_Checkout_Block_Cart_Item_Renderer_Failed
     */
    public function setItem(Varien_Object $item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     * Get quote item
     *
     * @return Varien_Object
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * Retrieve Product
     *
     * @return Mage_Catalog_Model_product
     */
    public function getProduct()
    {
        return $this->getItem();
    }

    /**
     * Retrieve product name
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->getProduct()->getName();
    }

    /**
     * Retrieve product price
     *
     * @return double
     */
    public function getProductPrice()
    {
        return $this->getProduct()->getFinalPrice();
    }

    /**
     * Retrieve calculated subtotal
     *
     * @return double
     */
    public function getSubtotal()
    {
        return $this->getProductPrice() * $this->getQty();
    }

    /**
     * Retrieve item's quantity
     *
     * @return int
     */
    public function getQty()
    {
        return $this->getProduct()->getQty();
    }

    /**
     * Check whether item is 'SKU failed'
     *
     * @return bool
     */
    public function isSkuFailedItem()
    {
        return !($this->_item instanceof Mage_Catalog_Model_Product);
    }

    /**
     * Retrieve item's message
     *
     * @return string
     */
    public function getMessage()
    {
        switch ($this->getItemCode()) {
            case Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_OUT_OF_STOCK:
                return $this->_getHelper()->getMessage(
                    Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_OUT_OF_STOCK
                );
            case Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED:
                $stockItem = Mage::getModel('Mage_CatalogInventory_Model_Stock_Item');
                $stockItem->loadByProduct($this->getProduct());
                return $this->_getHelper()->getMessage(
                    Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED
                )
                    . '<br>'
                    . $this->__("Only %d left in stock", $stockItem->getQty());
            case Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_CONFIGURE:
                return $this->_getHelper()->getMessage(
                    Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_CONFIGURE
                );
            case Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_SKU:
                return $this->_getHelper()->getMessage(
                    Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_SKU
                );
        }
    }

    /**
     * Retrieve item's SKU
     *
     * @return string
     */
    public function getFailedSku()
    {
        return $this->getProduct()->getSku();
    }

    /**
     * Retrieve delete URL for failed item
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl(
            'checkout/cart/removeFailed',
            array('sku' => $this->getFailedSku())
        );
    }

    /**
     * Get product thumbnail image
     *
     * @return Mage_Catalog_Model_Product_Image
     */
    public function getProductThumbnail()
    {
        return $this->helper('Mage_Catalog_Helper_Image')->init($this->getProduct(), 'thumbnail');
    }

    /**
     * Retrieve URL to item Product
     *
     * @return string
     */
    public function getProductUrl()
    {
        if (!is_null($this->_productUrl)) {
            return $this->_productUrl;
        }
        $product = $this->getProduct();
        return $product->getUrlModel()->getUrl($product);
    }

    /**
     * Retrieve product id
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->getProduct()->getId();
    }

    /**
     * Retrieve item's failed code
     *
     * @return string
     */
    public function getItemCode()
    {
        return $this->getItem()->getCode();
    }
}
