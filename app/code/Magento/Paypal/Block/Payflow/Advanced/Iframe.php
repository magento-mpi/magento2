<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payflow Advanced iframe block
 *
 * @category   Magento
 * @package    Magento_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Paypal_Block_Payflow_Advanced_Iframe extends Magento_Paypal_Block_Payflow_Link_Iframe
{
    /**
     * Set payment method code
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_paymentMethodCode = Magento_Paypal_Model_Config::METHOD_PAYFLOWADVANCED;
    }

    /**
     * Get frame action URL
     *
     * @return string
     */
    public function getFrameActionUrl()
    {
        return $this->getUrl('paypal/payflowadvanced/form', array('_secure' => true));
    }

    /**
     * Check sandbox mode
     *
     * @return bool
     */
    public function isTestMode()
    {
        $mode = $this->_paymentData
            ->getMethodInstance(Magento_Paypal_Model_Config::METHOD_PAYFLOWADVANCED)
            ->getConfigData('sandbox_flag');
        return (bool) $mode;
    }
}
