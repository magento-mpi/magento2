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
 * @package     Mage_Payment
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payment Centinel Controller
 *
 */
class Mage_Payment_CentinelController extends Mage_Core_Controller_Front_Action
{
    /**
     * Initialize validation
     */
    public function validateAction()
    {
        $this->loadLayout();
        $block = $this->getLayout()->getBlock('root');

        $request = $this->getRequest();
        $paymentData = $request->getParam('payment');
        $paymentMethod = $request->getParam('method');
        $paymentData['method'] = $paymentMethod;

        $validator = $this->_getCentinelValidator($paymentMethod);
        $block->setPaymentMethod($paymentMethod);

        try {
            $validator->setIsValidationLock(true);
            $this->_getPayment()->importData($paymentData);
            $validator->setIsValidationLock(false);
        } catch (Mage_Core_Exception $e) {
            $block->setValidationMessage($e->getMessage());
            $block->setValidationError(true);
            $this->renderLayout();
            return;
        } catch (Exception $e) {
            Mage::logException($e);
            $block->setValidationMessage(Mage::helper('payment')->__('Validation is filed.'));
            $block->setValidationError(true);
            $this->renderLayout();
            return;
        }

        $params = $this->_getPayment()->getMethodInstance()->getCentinelValidationData();

        if ($validator->lookup($params)) {
            $block
                ->setAcsUrl($validator->getAcsUrl())
                ->setPayload($validator->getPayload())
                ->setTermUrl($validator->getTermUrl())
                ->setTransactionId($validator->getTransactionId())
                ->setValidationEnrolled(true);
        } else {
            $isRequired = $this->_getPayment()->getMethodInstance()->isCentinelValidationRequired();
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

    /**
     * Authenticate when returned from bank
     */
    public function authenticateAction()
    {
        $this->loadLayout();

        $request = $this->getRequest();
        $PAResPayload = $request->getParam('PaRes');
        $MD = $request->getParam('MD');
        $paymentMethod = $request->getParam('method');

        $block = $this->getLayout()->getBlock('root');
        $block->setMethodCode($paymentMethod);

        $validator = $this->_getCentinelValidator($paymentMethod);

        if ($validator->authenticate($PAResPayload, $MD)) {
            $block->setAuthenticateComplete(true);
            $block->setValidationMessage(Mage::helper('payment')->__('Validation is complete. Please continue.'));
        } else {
            $isRequired = $this->_getPayment()->getMethodInstance()->isCentinelAuthenticationRequired();
            if ($isRequired) {
                $block->setAuthenticateComplete(false);
                $block->setValidationMessage(Mage::helper('payment')->__('Validation is failed. Please try again.'));
            } else {
                $block->setAuthenticateComplete(true);
                $block->setValidationMessage(Mage::helper('payment')->__('Please continue.'));
            }
        }
        $this->renderLayout();
    }

    /**
     * Return centinel validation model
     *
     * @param Mage_Payment_Model_Method_Abstract $paymetMethod
     * @return Mage_Payment_Model_Service_Centinel
     */
    private function _getCentinelValidator($paymetMethod)
    {
        $validator = Mage::getSingleton('payment/service_centinel');
        $validator->setPaymentMethodCode($paymetMethod)->setStore(Mage::app()->getStore());
        return $validator;
    }

    /**
     * Return payment model
     *
     * @return Mage_Sales_Model_Quote_Payment
     */
    private function _getPayment()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getPayment();
    }
}
