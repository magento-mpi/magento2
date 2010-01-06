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
 * @category   Mage
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * 3D Secure Validation Model
 */
class Mage_Payment_Model_Service_Centinel extends Varien_Object
{
    /**
     * States of validation
     *
     */
    const STATE_NO_VALIDATION = 'no_validation';
    const STATE_VALIDATION_NOT_ENROLLED = 'not_enrolled';
    const STATE_VALIDATION_ENROLLED  = 'enrolled';
    const STATE_AUTENTICATION_COMPLETE = 'complete';    
    const STATE_AUTENTICATION_FAILED   = 'failed';    

    /**
     * Validation api model
     *
     * @var Mage_Payment_Model_Service_Centinel_Api
     */
    protected $_api;

    /**
     * Validation session object
     *
     * @var Mage_Payment_Model_Service_Centinel_Session
     */
    protected $_session;

    /**
     * Code of payment method
     *
     * @var string
     */
    protected $_paymentMethodCode;

    /**
     * Return validation api model
     *
     * @return Mage_Payment_Model_Service_Centinel_Api
     */
    protected function _getApi()
    {
        if (!is_null($this->_api)) {
            return $this->_api;
        }

        $this->_api = Mage::getSingleton('payment/service_centinel_api');
        $this->_api
           ->setProcessorId($this->_getConfig('processor_id'))
           ->setMerchantId($this->_getConfig('merchant_id'))
           ->setTransactionPwd(Mage::helper('core')->decrypt($this->_getConfig('password')))
           ->setMapUrl($this->_getMapUrl());
        return $this->_api; 
    }

    /**
     * Return value from section of centinel config
     *
     * @param string $path
     * @return string
     */
    protected function _getConfig($path)
    {
        return Mage::getStoreConfig('payment_services/centinel/' . $path, $this->getStore());
    }

    /**
     * Return validation session object
     *
     * @return Mage_Payment_Model_Service_Centinel_Session
     */
    protected function _getSession()
    {
        if (!is_null($this->_session)) {
            return $this->_session;
        }
        $this->_session = Mage::getSingleton('payment/service_centinel_session');
        return $this->_session;
    }

    /**
     * Setter for data stored in session
     *
     * @param string $key
     * @param string $value
     * @return Mage_Payment_Model_Service_Centinel
     */
    protected function _setDataStoredInSession($key, $value)
    {
        $this->setData($key, $value);
        $key = $this->_paymentMethodCode . '_' . $key;
        $this->_getSession()->setData($key, $value);
        return $this;
    }

    /**
     * Getter for data stored in session
     *
     * @param string $key
     * @return string
     */
    protected function _getDataStoredInSession($key)
    {
        if ($this->getData($key)) {
            return $this->getData($key);
        }
        $key = $this->_paymentMethodCode . '_' . $key;
        return $this->_getSession()->getData($key);
    }

    /**
     * Return URL for Api  
     *
     * @return string
     */
    protected function _getMapUrl()
    {
        if ($this->_getConfig('test_mode')) {
            return 'https://centineltest.cardinalcommerce.com/maps/txns.asp';
        }
        return $this->getMapUrl();
    }

    /**
     * Return URL for term response from Centinel  
     *
     * @return string
     */
    public function getTermUrl()
    {
        $formKey = Mage::getSingleton('core/session')->getFormKey();
        if (Mage::app()->getStore()->isAdmin()) {
            return Mage::getUrl('*/payment_centinel/term', array('_secure' => true,'_current' => true, 'form_key' => $formKey, 'method' => $this->getPaymentMethodCode()));
        } else {
            return Mage::getUrl('payment/centinel/term', array('_secure' => true, 'form_key' => $formKey, 'method' => $this->getPaymentMethodCode()));
        }
    }

    /**
     * Return URL for Centinel validation request
     *
     * @return string
     */
    public function getValidationUrl()
    {
        $formKey = Mage::getSingleton('core/session')->getFormKey();
        if (Mage::app()->getStore()->isAdmin()) {
            return Mage::getUrl('*/payment_centinel/validate', array('_secure' => true, 'form_key' => $formKey, 'method' => $this->getPaymentMethodCode()));
        } else {
            return Mage::getUrl('payment/centinel/validate', array('_secure' => true, 'form_key' => $formKey, 'method' => $this->getPaymentMethodCode()));
        }
    }

    /**
     * Generate control sum for payment data
     *
     * @param string $ccNumber
     * @param string $ccExpMonth
     * @param string $ccExpYear
     * @param string $amount
     * @param string $currencyCode
     * @return string
     */
    protected function _generateEnrolledControlSum($ccNumber, $ccExpMonth, $ccExpYear, $amount, $currencyCode)
    {
        $separator = '_';
        return md5($ccNumber . $separator .
                   $ccExpMonth . $separator .
                   $ccExpYear . $separator .
                   (double)$amount . $separator .
                   $currencyCode);
    }

    /**
     * Payment code setter
     *
     * @param string $value
     * @return Mage_Payment_Model_Service_Centinel
     */
    public function setPaymentMethodCode($value)
    {
        $this->_paymentMethodCode = $value;
        return $this;
    }

    /**
     * Payment code getter
     *
     * @return string
     */
    public function getPaymentMethodCode()
    {
        return $this->_paymentMethodCode;
    }
    
    /**
     * Process lookup validation
     *
     * @param Varien_Object $data
     * @return bool
     */
    public function lookup($data)
    {
        $this->reset();
        $_api = $this->_getApi();
        $_api
            ->setCardNumber($data->getCardNumber())
            ->setCardExpMonth($data->getCardExpMonth())
            ->setCardExpYear($data->getCardExpYear())
            ->setAmount($data->getAmount())
            ->setCurrencyCode($data->getCurrencyCode())
            ->setOrderNumber($data->getOrderNumber())
            ->callLookup();

        if ($_api->getEnrolled() == 'Y' && !$_api->getErrorNo() && $_api->getAcsUrl()) {
            $this
                ->setAuthenticationStatus(self::STATE_VALIDATION_ENROLLED)
                ->setAcsUrl($_api->getAcsUrl())
                ->setPayload($_api->getPayload())
                ->setTransactionId($_api->getTransactionId())
                ->setEnrolled($_api->getEnrolled())
                ->setEnrolledControlSum(
                    $this->_generateEnrolledControlSum(
                        $data->getCardNumber(), $data->getCardExpMonth(), $data->getCardExpYear(), 
                        $data->getAmount(), $data->getCurrencyCode()
                    ));
                return true;
        }

        $this->setAuthenticationStatus(self::STATE_VALIDATION_NOT_ENROLLED);
        return false;
    }

    /**
     * Process authenticate validation
     *
     * @param string $PaResPayload
     * @param string $MD
     * @return bool
     */
    public function authenticate($PaResPayload, $MD)
    {
        $_api = $this->_getApi();

        $_api
            ->setPaResPayload($PaResPayload)
            ->setTransactionId($MD)
            ->callAuthentication();

        if ($_api->getErrorNo() == 0 && $_api->getSignature() == 'Y' && $_api->getPaResStatus() != 'N') {
            $this
                ->setAuthenticationStatus(self::STATE_AUTENTICATION_COMPLETE)
                ->setPaResStatus($_api->getPaResStatus())
                ->setCavv($_api->getCavv())
                ->setEciFlag($_api->getEciFlag())
                ->setXid($_api->getXid());
            return true;
        }

        $this->setAuthenticationStatus(self::STATE_AUTENTICATION_FAILED);
        return false;
    }

    /**
     * Validate payment data
     *
     * @param Varien_Object $data
     * @return bool
     */
    public function validate($data)
    {
        if ($this->getIsValidationLock()) {
            return true;
        }
        if ($this->getAuthenticationStatus() == self::STATE_NO_VALIDATION) {
            Mage::throwException(Mage::helper('payment')->__('Centinel validation is requered'));
        }
        if ($this->getAuthenticationStatus() == self::STATE_VALIDATION_NOT_ENROLLED) {
            if (!$this->getIsValidationRequired()) {
                return true;
            }
            Mage::throwException(Mage::helper('payment')->__('Centinel validation is filed. Please check information and try again'));
        }
        if ($this->getAuthenticationStatus() == self::STATE_VALIDATION_ENROLLED) {
            Mage::throwException(Mage::helper('payment')->__('Centinel validation is not complete. Please finish authorization in the Bank`s interface'));
        }
        if ($this->getAuthenticationStatus() == self::STATE_AUTENTICATION_COMPLETE) {
            if ($this->getEnrolledControlSum() == $this->_generateEnrolledControlSum(
                        $data->getCardNumber(), $data->getCardExpMonth(), $data->getCardExpYear(), 
                        $data->getAmount(), $data->getCurrencyCode())) {
                return true;
            }
            Mage::throwException(Mage::helper('payment')->__('Centinel validation is filed. Please check information. If You change information please revalidate it'));       
        }
        if ($this->getAuthenticationStatus() == self::STATE_AUTENTICATION_FAILED) {
            if (!$this->getIsAuthenticationRequired()) {
                return true;
            }
            Mage::throwException(Mage::helper('payment')->__('Centinel validation is filed. Please check information and try again'));
        }
    }

    /**
     * Reset data, api and state
     *
     * @return Mage_Payment_Model_Service_Centinel
     */
    public function reset()
    {
        $this
           ->setAuthenticationStatus(self::STATE_NO_VALIDATION)
           ->setAcsUrl(false)
           ->setPayload(false)
           ->setTransactionId(false)
           ->setPaResStatus(false)
           ->setCavv(false)
           ->setEciFlag(false)
           ->setXid(false)
           ->setEnrolled(false)
           ->setEnrolledControlSum(false);

        $this->_api = null;
        
        return $this;
    }

    /**
     * Setter for EnrolledControlSum
     *
     * @param string $value
     * @return Mage_Payment_Model_Service_Centinel
     */
    public function setEnrolledControlSum($value)
    {
        return $this->_setDataStoredInSession('EnrolledControlSum', $value);
    }

    /**
     * Getter for EnrolledControlSum
     *
     * @return string
     */
    public function getEnrolledControlSum()
    {
        return $this->_getDataStoredInSession('EnrolledControlSum');
    }

    /**
     * Setter for AuthenticationStatus
     *
     * @param string $value
     * @return Mage_Payment_Model_Service_Centinel
     */
    public function setAuthenticationStatus($value){
        return $this->_setDataStoredInSession('authenticationStatus', $value);    
    }

    /**
     * Getter for AuthenticationStatus
     *
     * @return string
     */
    public function getAuthenticationStatus()
    {
        if ($this->_getDataStoredInSession('authenticationStatus')) {
            return $this->_getDataStoredInSession('authenticationStatus');
        }
        return self::STATE_NO_VALIDATION;   
    }

    /**
     * Setter for AcsUrl
     *
     * @param string $value
     * @return Mage_Payment_Model_Service_Centinel
     */
    public function setAcsUrl($value)
    {
        return $this->_setDataStoredInSession('AcsUrl', $value);
    }

    /**
     * Getter for AcsUrl
     *
     * @return string
     */
    public function getAcsUrl()
    {
        return $this->_getDataStoredInSession('AcsUrl');
    }

    /**
     * Setter for Payload
     *
     * @param string $value
     * @return Mage_Payment_Model_Service_Centinel
     */
    public function setPayload($value)
    {
        return $this->_setDataStoredInSession('Payload', $value);
    }

    /**
     * Getter for Payload
     *
     * @return string
     */
    public function getPayload()
    {
        return $this->_getDataStoredInSession('Payload');
    }
    
    /**
     * Setter for TransactionId
     *
     * @param string $value
     * @return Mage_Payment_Model_Service_Centinel
     */
    public function setTransactionId($value)
    {
        return $this->_setDataStoredInSession('TransactionId', $value);
    }

    /**
     * Getter for TransactionId
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->_getDataStoredInSession('TransactionId');
    }

    /**
     * Setter for PaResStatus
     *
     * @param string $value
     * @return Mage_Payment_Model_Service_Centinel
     */
    public function setPaResStatus($value)
    {
        return $this->_setDataStoredInSession('PaResStatus', $value);
    }

    /**
     * Getter for PaResStatus
     *
     * @return string
     */
    public function getPaResStatus()
    {
        return $this->_getDataStoredInSession('PaResStatus');
    }

    /**
     * Setter for Cavv
     *
     * @param string $value
     * @return Mage_Payment_Model_Service_Centinel
     */
    public function setCavv($value)
    {
        return $this->_setDataStoredInSession('Cavv', $value);
    }

    /**
     * Getter for Cavv
     *
     * @return string
     */
    public function getCavv()
    {
        return $this->_getDataStoredInSession('Cavv');
    }

    /**
     * Setter for EciFlag
     *
     * @param string $value
     * @return Mage_Payment_Model_Service_Centinel
     */
    public function setEciFlag($value)
    {
        return $this->_setDataStoredInSession('EciFlag', $value);
    }

    /**
     * Getter for EciFlag
     *
     * @return string
     */
    public function getEciFlag()
    {
        return $this->_getDataStoredInSession('EciFlag');
    }

    /**
     * Setter for Xid
     *
     * @param string $value
     * @return Mage_Payment_Model_Service_Centinel
     */
    public function setXid($value)
    {
        return $this->_setDataStoredInSession('Xid', $value);
    }

    /**
     * Getter for Xid
     *
     * @return string
     */
    public function getXid()
    {
        return $this->_getDataStoredInSession('Xid');
    }

    /**
     * Setter for Enrolled
     *
     * @param string $value
     * @return Mage_Payment_Model_Service_Centinel
     */
    public function setEnrolled($value)
    {
        return $this->_setDataStoredInSession('Enrolled', $value);
    }

    /**
     * Gerrer for Enrolled
     *
     * @return string
     */
    public function getEnrolled()
    {
        return $this->_getDataStoredInSession('Enrolled');
    }
}
