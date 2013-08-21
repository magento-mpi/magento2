<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Form for adding products by SKU
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Sales_Order_Create_Sku_Errors
    extends Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Abstract
{
    /**
     * Returns url to configure item
     *
     * @return string
     */
    public function getConfigureUrl()
    {
        return $this->getUrl('*/sales_order_create/configureProductToAdd');
    }

    /**
     * Returns enterprise cart model with custom session for order create page
     *
     * @return Enterprise_Checkout_Model_Cart
     */
    public function getCart()
    {
        if (!$this->_cart) {
            $session = Mage::getSingleton('Magento_Adminhtml_Model_Session_Quote');
            $this->_cart = parent::getCart()->setSession($session);
        }
        return $this->_cart;
    }

    /**
     * Returns current store model
     *
     * @return Magento_Core_Model_Store
     */
    public function getStore()
    {
        $storeId = $this->getCart()->getSession()->getStoreId();
        return Mage::app()->getStore($storeId);
    }

    /**
     * Get title of button, that adds products to order
     *
     * @return string
     */
    public function getAddButtonTitle()
    {
        return __('Add Products to Order');
    }
}
