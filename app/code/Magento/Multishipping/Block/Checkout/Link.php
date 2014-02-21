<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Multishipping\Block\Checkout;

/**
 * Multishipping cart link
 */
class Link extends \Magento\View\Element\Template
{
    /**
     * Multishipping helper
     *
     * @var \Magento\Multishipping\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Multishipping\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Multishipping\Helper\Data $helper,
        array $data = array()
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->getUrl('multishipping/checkout', array('_secure'=>true));
    }

    /**
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return $this->helper->getQuote();
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->helper->isMultishippingCheckoutAvailable()) {
            return '';
        }
        return parent::_toHtml();
    }
}
