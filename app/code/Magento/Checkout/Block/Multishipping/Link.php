<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multishipping cart link
 */
namespace Magento\Checkout\Block\Multishipping;

class Link extends \Magento\Core\Block\Template
{
    /**
     * Checkout data
     *
     * @var \Magento\Checkout\Helper\Data
     */
    protected $_checkoutData;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\Checkout\Helper\Data $checkoutData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\Checkout\Helper\Data $checkoutData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = array()
    ) {
        $this->_checkoutData = $checkoutData;
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout/multishipping', array('_secure'=>true));
    }

    /**
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->_checkoutData->isMultishippingCheckoutAvailable()) {
            return '';
        }
        return parent::_toHtml();
    }
}
