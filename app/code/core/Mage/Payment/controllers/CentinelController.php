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
 * Direct Checkout Controller
 *
 */
class Mage_Payment_CentinelController extends Mage_Core_Controller_Front_Action
{
    public function getCentinelValidator($paymetMethod)
    {
        $validator = Mage::getSingleton('payment/service_centinel');
        $validator->setPaymentMethodCode($paymetMethod);
        return $validator;
    }

    public function getPayment()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getPayment();
    }
    
    public function validateAction()
    {
        $this->loadLayout();
        $block = $this->getLayout()->getBlock('root');

        $request = $this->getRequest();
        $paymentData = $request->getParam('payment');
        $paymentMethod = $request->getParam('method');
        $paymentData['method'] = $paymentMethod; 

        $validator = $this->getCentinelValidator($paymentMethod);
        $block->setPaymentMethod($paymentMethod);

        try {
            $validator->setIsValidationUnlock(true);
            $this->getPayment()->importData($paymentData);
            $validator->setIsValidationEnabled(false);
        } catch (Exception $e) {
            $block->setValidationError($e->getMessage());
            $this->renderLayout();
            return;
        }        

        $params = $this->getPayment()->getMethodInstance()->getCentinelValidationData();

        if ($validator->lookup($params)) {
            $block
                ->setAcsUrl($validator->getAcsUrl())
                ->setPayload($validator->getPayload())
                ->setTermUrl($validator->getTermUrl())
                ->setTransactionId($validator->getTransactionId())
                ->setValidationEnrolled(true)
                ->setValidationMessage(Mage::helper('payment')->__('Please pass validation'));
        } else {
            $isRequired = $this->getPayment()->getMethodInstance()->isCentinelValidationRequired();
            if ($isRequired) {
                $block->setValidationError(true);        		
                $block->setValidationMessage(Mage::helper('payment')->__('Centinel validation is filed. Please check information and try again'));
            } else {
               $block->setValidationComplete(true);
               $block->setValidationMessage(Mage::helper('payment')->__('Centinel validation is not complete. You can continue or check information and try again'));
            } 
        }

        $this->renderLayout();
    }

    public function termAction()
    {
        $this->loadLayout();

        $request = $this->getRequest();
        $PAResPayload = $this->getRequest()->getParam('PaRes');
        $MD = $this->getRequest()->getParam('MD');
        $paymentMethod = $request->getParam('method');

        $block = $this->getLayout()->getBlock('root');
        $block->setMethodCode($paymentMethod);
        
        $validator = $this->getCentinelValidator($paymentMethod);

        if ($validator->authenticate($PAResPayload, $MD)) {
            $block->setAuthenticateComplete(true);
            $block->setValidationMessage(Mage::helper('payment')->__('Validation is complete. Please continue.'));
        } else {
            $block->setAuthenticateComplete(false);
            $block->setValidationMessage(Mage::helper('payment')->__('Validation is failed. Please try again.'));
        }

        $this->renderLayout();
    }

}
