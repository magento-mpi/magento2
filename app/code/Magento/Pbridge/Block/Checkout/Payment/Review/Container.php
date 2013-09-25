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
class Magento_Pbridge_Block_Checkout_Payment_Review_Container extends Magento_Core_Block_Template
{
    /**
     * Checkout session
     *
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * Construct
     *
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Custom rewrite for _toHtml() method
     * @return string
     */
    protected function _toHtml()
    {
        $quote = $this->_checkoutSession->getQuote();
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
