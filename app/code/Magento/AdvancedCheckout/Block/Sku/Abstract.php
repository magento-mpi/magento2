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
 * Abstract block of form with SKUs data
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 */
abstract class Magento_AdvancedCheckout_Block_Sku_Abstract
    extends Magento_Core_Block_Template
{
    /**
     * Retrieve form action URL
     *
     * @return string
     */
    abstract public function getFormAction();

    /**
     * Get request parameter name of SKU file imported flag
     *
     * @return string
     */
    public function getRequestParameterSkuFileImportedFlag()
    {
        return Magento_AdvancedCheckout_Helper_Data::REQUEST_PARAMETER_SKU_FILE_IMPORTED_FLAG;
    }

    /**
     * Check whether form should be multipart
     *
     * @return bool
     */
    public function getIsMultipart()
    {
        return false;
    }

    /**
     * Get link to "Order by SKU" on customer's account page
     *
     * @return string
     */
    public function getLink()
    {
        $data = $this->getData();
        if (empty($data['link_display']) || empty($data['link_text'])) {
            return '';
        }

        /** @var $helper Magento_AdvancedCheckout_Helper_Data */
        $helper = Mage::helper('Magento_AdvancedCheckout_Helper_Data');
        if (!$helper->isSkuEnabled() || !$helper->isSkuApplied()) {
            return '';
        }

        return '<a href="' . $helper->getAccountSkuUrl() . '">'
            . $this->escapeHtml($data['link_text']) . '</a>';
    }
}
