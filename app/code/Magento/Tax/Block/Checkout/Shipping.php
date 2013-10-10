<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Subtotal Total Row Renderer
 */
namespace Magento\Tax\Block\Checkout;

class Shipping extends \Magento\Checkout\Block\Total\DefaultTotal
{
    /**
     * Template path
     *
     * @var string
     */
    protected $_template = 'checkout/shipping.phtml';

    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $_taxConfig;

    /**
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Tax\Model\Config $taxConfig,
        array $data = array()
    ) {
        $this->_taxConfig = $taxConfig;
        parent::__construct($catalogData, $coreData, $context, $salesConfig, $customerSession, $checkoutSession,
            $storeManager, $data);
    }

    /**
     * Check if we need display shipping include and exclude tax
     *
     * @return bool
     */
    public function displayBoth()
    {
        return $this->_taxConfig->displayCartShippingBoth($this->getStore());
    }

    /**
     * Check if we need display shipping include tax
     *
     * @return bool
     */
    public function displayIncludeTax()
    {
        return $this->_taxConfig->displayCartShippingInclTax($this->getStore());
    }

    /**
     * Get shipping amount include tax
     *
     * @return float
     */
    public function getShippingIncludeTax()
    {
        return $this->getTotal()->getAddress()->getShippingInclTax();
    }

    /**
     * Get shipping amount exclude tax
     *
     * @return float
     */
    public function getShippingExcludeTax()
    {
        return $this->getTotal()->getAddress()->getShippingAmount();
    }

    /**
     * Get label for shipping include tax
     *
     * @return float
     */
    public function getIncludeTaxLabel()
    {
        return __('Shipping Incl. Tax (%1)', $this->escapeHtml($this->getTotal()->getAddress()->getShippingDescription()));
    }

    /**
     * Get label for shipping exclude tax
     *
     * @return float
     */
    public function getExcludeTaxLabel()
    {
        return __('Shipping Excl. Tax (%1)', $this->escapeHtml($this->getTotal()->getAddress()->getShippingDescription()));
    }
}
