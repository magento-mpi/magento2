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
 * "Add by SKU" accordion
 *
 * @method string                                                   getHeaderText()
 * @method Enterprise_Checkout_Block_Adminhtml_Manage_Accordion_Sku setHeaderText()
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Manage_Accordion_Sku extends Enterprise_Checkout_Block_Adminhtml_Sku_Abstract
{
    /**
     * Define accordion header
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setHeaderText(__('Add to Shopping Cart by SKU'));
    }

    /**
     * Register grid with instance of AdminCheckout, register new list type and URL to fetch configure popup HTML
     *
     * @return string
     */
    public function getAdditionalJavascript()
    {
        // Origin of configure popup HTML
        $js = $this->getJsOrderObject() . ".addSourceGrid({htmlId: \"{$this->getId()}\", "
            . "listType: \"{$this->getListType()}\"});";
        $js .= $this->getJsOrderObject() . ".addNoCleanSource('{$this->getId()}');";
        $js .= 'addBySku.observeAddToCart();';
        return $js;
    }

    /**
     * Retrieve JavaScript AdminCheckout instance name
     *
     * @return string
     */
    public function getJsOrderObject()
    {
        return 'checkoutObj';
    }

    /**
     * Retrieve container ID for error grid
     *
     * @return string
     */
    public function getErrorGridId()
    {
        return 'checkout_errors';
    }

    /**
     * Retrieve file upload URL
     *
     * @return string
     */
    public function getFileUploadUrl()
    {
        return $this->getUrl('*/checkout/uploadSkuCsv');
    }

    /**
     * Retrieve context specific JavaScript
     *
     * @return string
     */
    public function getContextSpecificJs()
    {
        return 'Event.observe(window, \'load\', initSku);';
    }
}
