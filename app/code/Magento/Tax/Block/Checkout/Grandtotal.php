<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tax\Block\Checkout;

/**
 * Subtotal Total Row Renderer
 */
class Grandtotal extends \Magento\Checkout\Block\Total\DefaultTotal
{
    /**
     * Path to template file
     *
     * @var string
     */
    protected $_template = 'checkout/grandtotal.phtml';

    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $_taxConfig;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Tax\Model\Config $taxConfig,
        array $data = []
    ) {
        $this->_taxConfig = $taxConfig;
        parent::__construct($context, $customerSession, $checkoutSession, $salesConfig, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Check if we have include tax amount between grandtotal incl/excl tax
     *
     * @return bool
     */
    public function includeTax()
    {
        if ($this->getTotal()->getAddress()->getGrandTotal()) {
            return $this->_taxConfig->displayCartTaxWithGrandTotal($this->getStore());
        }
        return false;
    }

    /**
     * Get grandtotal exclude tax
     *
     * @return float
     */
    public function getTotalExclTax()
    {
        $excl = $this->getTotal()->getAddress()->getGrandTotal() - $this->getTotal()->getAddress()->getTaxAmount();
        $excl = max($excl, 0);
        return $excl;
    }
}
