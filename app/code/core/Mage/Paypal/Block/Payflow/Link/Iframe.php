<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payflow link iframe block
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Block_Payflow_Link_Iframe extends Mage_Payment_Block_Form
{
    /**
     * Get frame action URL
     *
     * @return string
     */
    public function getFrameActionUrl()
    {
        return $this->getUrl('paypal/payflow/form', array('_secure' => true));
    }

    /**
     * Get secure token
     *
     * @return string
     */
    public function getSecureToken()
    {
        return $this->_getSalesDocument()
            ->getPayment()
            ->getAdditionalInformation('secure_token');
    }

    /**
     * Get secure token ID
     *
     * @return string
     */
    public function getSecureTokenId()
    {
        return $this->_getSalesDocument()
            ->getPayment()
            ->getAdditionalInformation('secure_token_id');
    }

    /**
     * Get payflow transaction URL
     *
     * @return string
     */
    public function getTransactionUrl()
    {
        return Mage_Paypal_Model_Payflowlink::TRANSACTION_PAYFLOW_URL;
    }

    /**
     * Check sandbox mode
     *
     * @return bool
     */
    public function isTestMode()
    {
        $mode = Mage::helper('Mage_Payment_Helper_Data')
            ->getMethodInstance(Mage_Paypal_Model_Config::METHOD_PAYFLOWLINK)
            ->getConfigData('sandbox_flag');
        return (bool) $mode;
    }

    /**
     * Get sales document object
     *
     * @return Mage_Sales_Model_Order|Mage_Sales_Model_Quote
     */
    protected function _getSalesDocument()
    {
       return Mage::getSingleton('Mage_Checkout_Model_Session')->getQuote();
    }
}
