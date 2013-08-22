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
 * Add by SKU errors accordion
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdvancedCheckout_Block_Adminhtml_Manage_Accordion_Sku_Errors
    extends Magento_AdvancedCheckout_Block_Adminhtml_Sku_Errors_Abstract
{
    /**
     * Returns url to configure item
     *
     * @return string
     */
    public function getConfigureUrl()
    {
        $customer = Mage::registry('checkout_current_customer');
        $store = Mage::registry('checkout_current_store');
        $params = array(
            'customer'   => $customer->getId(),
            'store'    => $store->getId()
        );
        return $this->getUrl('*/checkout/configureProductToAdd', $params);
    }

    /**
     * Retrieve additional JavaScript for error grid
     *
     * @return string
     */
    public function getAdditionalJavascript()
    {
        return "addBySku.addErrorSourceGrid({htmlId: '{$this->getId()}', listType: '{$this->getListType()}'})";
    }

    /**
     * Returns current store model
     *
     * @return Magento_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::registry('checkout_current_store');
    }

    /**
     * Get title of button, that adds products to shopping cart
     *
     * @return string
     */
    public function getAddButtonTitle()
    {
        return __('Add to Shopping Cart');
    }
}
