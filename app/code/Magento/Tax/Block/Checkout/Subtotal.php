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

class Subtotal extends \Magento\Checkout\Block\Total\DefaultTotal
{
    /**
     * Template path
     *
     * @var string
     */
    protected $_template = 'checkout/subtotal.phtml';

    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $_taxConfig;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Tax\Model\Config $taxConfig,
        array $data = array()
    ) {
        $this->_taxConfig = $taxConfig;
        parent::__construct($context, $catalogData, $customerSession, $checkoutSession, $salesConfig, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * @return bool
     */
    public function displayBoth()
    {
        return $this->_taxConfig->displayCartSubtotalBoth($this->getStore());
    }
}
