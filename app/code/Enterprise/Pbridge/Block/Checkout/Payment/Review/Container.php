<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Dibs payment block
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento
 */
class Enterprise_Pbridge_Block_Checkout_Payment_Review_Container extends Magento_Core_Block_Template
{
    /**
     * Custom rewrite for _toHtml() method
     * @return string
     */
    protected function _toHtml()
    {
        $quote = Mage::getSingleton('Magento_Checkout_Model_Session')->getQuote();
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
