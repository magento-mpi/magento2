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
namespace Magento\AdvancedCheckout\Block\Sku;

abstract class AbstractSku
    extends \Magento\Core\Block\Template
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
     * @var \Magento\AdvancedCheckout\Helper\Data
     */
    protected $_checkoutData = null;

    /**
     * @param \Magento\AdvancedCheckout\Helper\Data $checkoutData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\AdvancedCheckout\Helper\Data $checkoutData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
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
        return \Magento\AdvancedCheckout\Helper\Data::REQUEST_PARAMETER_SKU_FILE_IMPORTED_FLAG;
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

        /** @var $helper \Magento\AdvancedCheckout\Helper\Data */
        $helper = $this->_checkoutData;
        if (!$helper->isSkuEnabled() || !$helper->isSkuApplied()) {
            return '';
        }

        return '<a href="' . $helper->getAccountSkuUrl() . '">'
            . $this->escapeHtml($data['link_text']) . '</a>';
    }
}
