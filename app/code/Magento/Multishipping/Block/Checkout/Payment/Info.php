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
 * Multishipping checkout payment information data
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Multishipping\Block\Checkout\Payment;

class Info extends \Magento\Payment\Block\Info\AbstractContainer
{
    /**
     * @var \Magento\Multishipping\Model\Checkout\Type\Multishipping
     */
    protected $_multishipping;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Multishipping\Model\Checkout\Type\Multishipping $multishipping
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Multishipping\Model\Checkout\Type\Multishipping $multishipping,
        array $data = array()
    ) {
        $this->_multishipping = $multishipping;
        parent::__construct($context, $paymentData, $data);
    }

    /**
     * Retrieve payment info model
     *
     * @return \Magento\Payment\Model\Info
     */
    public function getPaymentInfo()
    {
        return $this->_multishipping->getQuote()->getPayment();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $html = '';
        $block = $this->getChildBlock($this->_getInfoBlockName());
        if ($block) {
            $html = $block->toHtml();
        }
        return $html;
    }
}
