<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payment information import/export model
 * Collects and provides access to PayPal-specific payment data
 */
class Mage_Paypal_Model_Info
{
    /**
     * All payment information map
     * @var array
     */
    protected $_paymentMap = array(
        'payer_id'     => 'paypal_payer_id',
        'email'        => 'paypal_payer_email',
        'payer_status' => 'paypal_payer_status',
        'correlation_id' => 'paypal_correlation_id',
        'address_id'     => 'paypal_address_id',
        'address_status' => 'paypal_address_status',
        'protection_eligibility' => 'paypal_protection_eligibility',
    );

    /**
     * Map of payment information available to customer
     * @var array
     */
    protected $_paymentPublicMap = array(
        'paypal_payer_id',
        'paypal_payer_email',
    );

    /**
     * Rendered payment map cache
     * @var array
     */
    protected $_paymentMapFull = array();

//    /**
//     * Payment info map getter
//     * @return array
//     */
//    public function getPaymentInfoMap()
//    {
//        return $this->_paymentMap;
//    }

    /**
     * All available payment info getter
     * @param Mage_Payment_Model_Info $payment
     * @param bool $labelValuesOnly
     * @return array
     */
    public function getPaymentInfo(Mage_Payment_Model_Info $payment, $labelValuesOnly = false)
    {
        return $this->_getFullInfo(array_values($this->_paymentMap), $payment, $labelValuesOnly);
    }

    /**
     * Public payment info getter
     * @param Mage_Payment_Model_Info $payment
     * @param bool $labelValuesOnly
     * @return array
     */
    public function getPublicPaymentInfo(Mage_Payment_Model_Info $payment, $labelValuesOnly = false)
    {
        return $this->_getFullInfo($this->_paymentPublicMap, $payment, $labelValuesOnly);
    }

    /**
     * Grab data from source and map it into payment
     * @param array|Varien_Object|callback $from
     * @param Mage_Payment_Model_Info $payment
     * @param array $map
     */
    public function importToPayment($from, Mage_Payment_Model_Info $payment, array $map = null)
    {
        Varien_Object_Mapper::accumulateByMap($from, array($payment, 'setAdditionalInformation'),
            $map ? $map : $this->_paymentMap
        );
    }

    /**
     * Grab data from payment and map it into target
     * @param Mage_Payment_Model_Info $payment
     * @param array|Varien_Object|callback $to
     * @param array $map
     * @return array|Varien_Object
     */
    public function &exportFromPayment(Mage_Payment_Model_Info $payment, $to, array $map = null)
    {
        Varien_Object_Mapper::accumulateByMap(array($payment, 'getAdditionalInformation'), $to,
            $map ? $map : array_flip($this->_paymentMap)
        );
        return $to;
    }

    /**
     * Render info item
     * @param array $keys
     * @param Mage_Payment_Model_Info $payment
     * @param bool $labelValuesOnly
     */
    protected function _getFullInfo(array $keys, Mage_Payment_Model_Info $payment, $labelValuesOnly)
    {
        $result = array();
        foreach ($keys as $key) {
            if (!isset($this->_paymentMapFull[$key])) {
                $this->_paymentMapFull[$key] = array();
            }
            if (!isset($this->_paymentMapFull[$key]['label'])) {
                $this->_paymentMapFull[$key]['label'] = $this->_getLabel($key);
                $this->_paymentMapFull[$key]['value'] = $payment->getAdditionalInformation($key);
            }
            if (!empty($this->_paymentMapFull[$key]['value'])) {
                if ($labelValuesOnly) {
                    $result[$this->_paymentMapFull[$key]['label']] = $this->_paymentMapFull[$key]['value'];
                } else {
                    $result[$key] = $this->_paymentMapFull[$key];
                }
            }
        }
        return $result;
    }

    /**
     * Render info item labels
     * @param string $key
     */
    protected function _getLabel($key)
    {
        switch ($key) {
            case 'paypal_payer_id':
                return Mage::helper('paypal')->__('Customer ID');
            case 'paypal_payer_email':
                return Mage::helper('paypal')->__('Customer Email');
            case 'paypal_payer_status':
                return Mage::helper('paypal')->__('Payer Status');
            case 'paypal_correlation_id':
                return Mage::helper('paypal')->__('Corellation ID');
            case 'paypal_address_id':
                return Mage::helper('paypal')->__('Customer Address ID');
            case 'paypal_avs_status':
                return Mage::helper('paypal')->__('Street Address Status');
            case 'paypal_protection_eligibility':
                return Mage::helper('paypal')->__('Protection Eligibility');
            case 'paypal_centinel_verified':
                return Mage::helper('paypal')->__('3D Secure Verification Result');
        }
        return '';
    }
}
