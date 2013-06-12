<?php
/**
 * Magento Saas Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Saas Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Saas
 * @package     Saas_Payment
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

 /**
 * Saas Payment Statistics Observer model
 *
 * @category   Saas
 * @package    Saas_Payment
 * @author     Magento Saas Team <core@magentocommerce.com>
 */
class Saas_Payment_Model_Statistics_Observer
{
    /*
     * Array of allowed methods
     */
    protected $_paymentMethodTestFlag = array(
        'pbridge_ogone_direct'           => 'payment/pbridge_ogone_direct/sandbox_flag',
        'sagepay_direct'                 => 'payment/sagepay_direct/mode',
        'pbridge_psigate_basic'          => 'payment/psigate_basic/test',
        'pbridge_authorizenet'           => 'payment/authorizenet/test',
        'pbridge_worldpay_direct'        => 'payment/worldpay_direct/test',
        'ccavenue'                       => 'payment/ccavenue/test',
        'cardgate'                       => 'payment/cardgate/test',
        'pbridge_eway_direct'            => 'payment/eway_direct/test',
        'pbridge_cybersource_soap'       => 'payment/cybersource_soap/test',
        'pbridge_payone_gate'            => 'payment/payone_gate/test',
        'pbridge_firstdata'              => 'payment/firstdata/test',
        'pbridge_dibs'                   => 'payment/dibs/test',
        'pbridge_verisign'               => 'payment/verisign/sandbox_flag',
        'pbridge_paypal_direct'          => 'paypal/wpp/sandbox_flag',
        'pbridge_paypal_direct_boarding' => 'paypal/onboarding/sandbox_flag',
        'pbridge_payone_debit'           => 'payment/pbridge_payone_debit/test',
        'cardgate_creditcard'            => 'payment/cardgate/test_mode',
        'cardgate_sofortbanking'         => 'payment/cardgate/test_mode',
        'cardgate_ideal'                 => 'payment/cardgate/test_mode',
        'cardgate_mistercash'            => 'payment/cardgate/test_mode',
        'pbridge_braintree_basic'        => 'payment/braintree_basic/environment',
        'pbridge_ogone_direct_debit'     => 'payment/pbridge_ogone_direct_debit/sandbox_flag',
        'paypal_express'                 => 'paypal/wpp/sandbox_flag',
        'paypaluk_express'               => null,
        'paypal_standard'                => 'payment/paypal_standard/sandbox_flag',
        'pbridge_paypaluk_direct'        => 'paypal/wpuk/sandbox_flag',
        'paypal_billing_agreement'       => 'paypal/wpp/sandbox_flag',
        'hosted_pro'                     => 'paypal/wpp/sandbox_flag',
        'paypal_express_boarding'        => 'paypal/onboarding/sandbox_flag',
    );

    /**
     * Return payment test flag
     *
     * @param  $payment
     * @return bool
     */
    protected function _getPaymentTestFlag($payment)
    {
        $method = $payment->getMethod();
        if (array_key_exists($method, $this->_paymentMethodTestFlag)) {
            $path = $this->_paymentMethodTestFlag[$method];
            $testFlag = isset($path) ? Mage::getStoreConfig($path) : $this->_getCustomSandboxFlag($method);
            if (in_array($testFlag, array('test', 'sim', 'true', '1', 'sandbox'))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Set is test flag to order
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function beforeOrderPlace(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();
        $order->setIsTest($this->_getPaymentTestFlag($order->getPayment()));
    }

    /**
     * Get sandbox flag in customizable way
     *
     * @param string $method
     * @return int|string
     */
    protected function _getCustomSandboxFlag($method)
    {
        if ($method == Mage_Paypal_Model_Config::METHOD_WPP_PE_EXPRESS) {
            /** @var $config Mage_Paypal_Model_Config */
            $config = Mage::getModel('Mage_Paypal_Model_Config');
            $config->setMethod($method);
            return $config->sandboxFlag;
        }
        return 0;
    }
}
