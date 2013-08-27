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
 * Abstract block of form with SKUs data
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 */
abstract class Enterprise_Checkout_Block_Sku_Abstract
    extends Magento_Core_Block_Template
{
    /**
     * Retrieve form action URL
     *
     * @return string
     */
    abstract public function getFormAction();

    /**
     * Checkout data
     *
     * @var Enterprise_Checkout_Helper_Data
     */
    protected $_checkoutData = null;

    /**
     * @param Enterprise_Checkout_Helper_Data $checkoutData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_Checkout_Helper_Data $checkoutData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_checkoutData = $checkoutData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get request parameter name of SKU file imported flag
     *
     * @return string
     */
    public function getRequestParameterSkuFileImportedFlag()
    {
        return Enterprise_Checkout_Helper_Data::REQUEST_PARAMETER_SKU_FILE_IMPORTED_FLAG;
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

        /** @var $helper Enterprise_Checkout_Helper_Data */
        $helper = $this->_checkoutData;
        if (!$helper->isSkuEnabled() || !$helper->isSkuApplied()) {
            return '';
        }

        return '<a href="' . $helper->getAccountSkuUrl() . '">'
            . $this->escapeHtml($data['link_text']) . '</a>';
    }
}
