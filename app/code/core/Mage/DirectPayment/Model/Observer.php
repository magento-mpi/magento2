<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
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
 * @category    Mage
 * @package     Mage_DirectPayment
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * DirectPayment observer
 *
 * @category    Mage
 * @package     Mage_DirectPayment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_DirectPayment_Model_Observer
{
    /**
     * Save order into registry to use it in the overloaded controller.
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_DirectPayment_Model_Observer
     */
    public function saveOrderAfterSubmit(Varien_Event_Observer $observer)
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getData('order');
        Mage::register('directpayment_order', $order, true);
        
        return $this;
    }
    
	/**
     * Save order into registry to use it in the overloaded controller.
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_DirectPayment_Model_Observer
     */
    public function addAdditionalFieldsToResponse(Varien_Event_Observer $observer)
    {
        /* @var $controller Mage_Checkout_OnepageController */
        $controller = $observer->getEvent()->getData('controller_action');
        /* @var $order Mage_Sales_Model_Order */
        $order = Mage::registry('directpayment_order');
        if ($order){
            $payment = $order->getPayment();
            if ($payment && $payment->getMethod() == 'directpayment'){
                //return json with data.
                $result = Mage::helper('core')->jsonDecode($controller->getResponse()->getBody('default'), Zend_Json::TYPE_ARRAY);
                
                if (empty($result['error'])){
                    //if is success, then add new fields
                    //TEMPORARY FOR TEST ONLY. Will be refactored.
                    $merchant_api_login_id = '36sCtGS8w';
                    $merchant_transaction_key = '7UWKj2Y6B3s74dY4';
                    $amount = $order->getGrandTotal();
                    $fp_sequence = $order->getIncrementId();
                    $fp_timestamp = time();
                    $hash = hash_hmac("md5", $merchant_api_login_id . "^" . $fp_sequence . "^" . $fp_timestamp . "^" . $amount . "^", $merchant_transaction_key);
                    $result['directpayment'] = array(
                        'x_relay_response' => 'TRUE',
                        'x_version' => '3.1',
                        'x_delim_char' => ',',
                        'x_delim_data' => 'TRUE',
                        'x_amount' => $amount,
                        'x_fp_sequence' => $fp_sequence,
                        'x_fp_hash' => $hash,
                        'x_fp_timestamp' => $fp_timestamp,
                        'x_relay_url' => 'http://kd.varien.com/dev/andrey.moskvenkov/direct_post/direct_post.php',
                        'x_login' => $merchant_api_login_id
                    );
                    //
                    
                    $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                }
            }
        }
        
        return $this;
    }
}
