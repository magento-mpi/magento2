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
 * @package     Mage_Centinel
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Centinel Index Controller
 *
 */
class Mage_Centinel_Adminhtml_Centinel_IndexController extends Mage_Adminhtml_Controller_Action
{
        
    /**
     * Process validate payment data action
     *
     */
    public function validatePaymentDataAction()
    {
        $result = array();
        try {
            $paymentData = $this->getRequest()->getParam('payment');
            $validator = $this->_getValidator();
            if (!$validator) {
                Mage::throwException(Mage::helper('centinel')->__('This payment method is not have centinel validation'));
            }
            $validator->skipValidation(true);
            $this->_getPayment()->importData($paymentData);
            $validator->skipValidation(false);
            
            $lookupPaymentData = $this->_getPayment()->getMethodInstance()->getCentinelValidationData();
            $validator->lookup($lookupPaymentData);
        
            if ($validator->isAuthenticationAllow()) {
                $result['authenticationUrl'] = $validator->getAuthenticationStartUrl();
            } else {
                $isRequired = $this->_getPayment()->getMethodInstance()->getIsCentinelValidationRequired();
                if ($isRequired) {
                    Mage::throwException(Mage::helper('centinel')->__('Centinel validation is filed. Please check information and try again'));
                } else {
                    Mage::throwException(Mage::helper('centinel')->__('Centinel validation is not complete. You can continue or check information and try again'));
                } 
            }
        } catch (Mage_Core_Exception $e) {
            $result['message'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $result['message'] = Mage::helper('centinel')->__('Validation failed.');
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));        
    }
    
    /**
     * Process autentication start action
     *
     */
    public function authenticationStartAction()
    {
        if ($validator = $this->_getValidator()) {
            Mage::register('centinel_validator', $validator);    
        }
        $this->loadLayout()->renderLayout();
    }

    /**
     * Process autentication complete action
     *
     */
    public function authenticationCompleteAction()
    {
        if ($validator = $this->_getValidator()) {
            $request = $this->getRequest();
            $PAResPayload = $request->getParam('PaRes');
            $MD = $request->getParam('MD');
            $validator->authenticate($PAResPayload, $MD);
            Mage::register('centinel_validator', $validator);
        }        
                
        $this->loadLayout()->renderLayout();
    }
    
    /**
     * Return payment model
     *
     * @return Mage_Sales_Model_Quote_Payment
     */
    private function _getPayment()
    {
        $model = Mage::getSingleton('adminhtml/sales_order_create');
        return $model->getQuote()->getPayment();
    }

    /**
     * Return Centinel validation model
     *
     * @return Mage_Centinel_Model_Service
     */
    private function _getValidator()
    {
        if ($this->_getPayment()->getMethodInstance()->getIsCentinelValidationEnabled()) {
            return $this->_getPayment()->getMethodInstance()->getCentinelValidator();
        }
        return false;
    }
}
