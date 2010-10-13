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
                    $requestToPaygate = $payment->getMethodInstance()->generateRequestFromOrder($order);
                    $result['directpayment'] = array('fields' => $requestToPaygate->getData());
                    
                    $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                }
            }
        }
        
        return $this;
    }
}
