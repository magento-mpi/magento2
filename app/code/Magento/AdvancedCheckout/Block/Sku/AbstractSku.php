<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract block of form with SKUs data
 *
 */
namespace Magento\AdvancedCheckout\Block\Sku;

abstract class AbstractSku extends \Magento\Framework\View\Element\Template
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
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\AdvancedCheckout\Helper\Data $checkoutData
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\AdvancedCheckout\Helper\Data $checkoutData,
        \Magento\Framework\Math\Random $mathRandom,
        array $data = array()
    ) {
        $this->_checkoutData = $checkoutData;
        $this->mathRandom = $mathRandom;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
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

        return '<a href="' . $helper->getAccountSkuUrl() . '" class="action links">' . $this->escapeHtml($data['link_text']) . '</a>';
    }

    /**
     * Get random string
     *
     * @param int $length
     * @param string|null $chars
     * @return string
     */
    public function getRandomString($length, $chars = null)
    {
        return $this->mathRandom->getRandomString($length, $chars);
    }
}
