<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Dibs payment block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento
 */
namespace Magento\Pbridge\Block\Checkout\Payment\Review;

class Container extends \Magento\Core\Block\Template
{
    /**
     * Custom rewrite for _toHtml() method
     * @return string
     */
    protected function _toHtml()
    {
        $quote = \Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote();
        if ($quote) {
            $payment = $quote->getPayment();
            if ($payment->getMethodInstance()->getIsDeferred3dCheck()) {
                $this->setMethodCode($payment->getMethod());
                return parent::_toHtml();
            }
        }

        return '';
    }
}
