<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Form for adding products by SKU
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Sales\Order\Create\Sku;

class Errors
    extends \Magento\AdvancedCheckout\Block\Adminhtml\Sku\Errors\AbstractErrors
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
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    public function getCart()
    {
        if (!$this->_cart) {
            $session = \Mage::getSingleton('Magento\Adminhtml\Model\Session\Quote');
            $this->_cart = parent::getCart()->setSession($session);
        }
        return $this->_cart;
    }

    /**
     * Returns current store model
     *
     * @return \Magento\Core\Model\Store
     */
    public function getStore()
    {
        $storeId = $this->getCart()->getSession()->getStoreId();
        return \Mage::app()->getStore($storeId);
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
