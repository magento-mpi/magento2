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
 * @package    Mage_Centinel
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * 3D Secure Validation Model
 */
class Mage_Centinel_Model_Service extends Varien_Object
{
    /**
     * States of validation
     *
     */
    const STATE_NEED_VALIDATION = 'need_validation';
    const STATE_VALIDATION_FAILED = 'validation_failed';
    const STATE_VALIDATION_SUCCESSFUL = 'validation_successful';
    const STATE_AUTENTICATION_FAILED = 'autentication_failed';
    const STATE_AUTENTICATION_SUCCESSFUL = 'autentication_successful';

    /**
     * Cmpi result`s fields
     *
     */
    const CMPI_FIELD_PARES = 'pa_res_status';
    const CMPI_FIELD_ENROLLED = 'enrolled';
    const CMPI_FIELD_CAVV = 'cavv';
    const CMPI_FIELD_ECI = 'eci_flag';
    const CMPI_FIELD_XID = 'xid';

    /**
     * Cmpi data map - keys for public usage
     *
     * @var array
     */
    protected $_cmpiMap = array(
        self::CMPI_FIELD_PARES => 'centinel_mpivendor',
        self::CMPI_FIELD_ENROLLED => 'centinel_authstatus',
        self ::CMPI_FIELD_CAVV => 'centinel_cavv',
        self::CMPI_FIELD_ECI => 'centinel_eci',
        self::CMPI_FIELD_XID => 'centinel_xid'
    );

    /**
     * Validation api model
     *
     * @var Mage_Centinel_Model_Api
     */
    protected $_api;

    /**
     * Validation session object
     *
     * @var Mage_Centinel_Model_Session
     */
    protected $_session;

    /**
     * Code of payment method
     *
     * @var string
     */
    protected $_paymentMethodCode;

    /**
     * Flag - if it is true, self::validate return true always
     *
     * @var bool
     */
    protected $_skipValidation = false;
    
    /**
     * Return validation api model
     *
     * @return Mage_Centinel_Model_Api
     */
    protected function _getApi()
    {
        if (!is_null($this->_api)) {
            return $this->_api;
        }

        $this->_api = Mage::getSingleton('centinel/api');
        $this->_api
           ->setProcessorId($this->_getConfig('processor_id'))
           ->setMerchantId($this->_getConfig('merchant_id'))
           ->setTransactionPwd(Mage::helper('core')->decrypt($this->_getConfig('password')))
           ->setIsTestMode((bool)(int)$this->_getConfig('test_mode'))
           ->setApiEndpointUrl($this->getCustomApiEndpointUrl());
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
     * @return Mage_Centinel_Model_Session
     */
    protected function _getSession()
    {
        if (!is_null($this->_session)) {
            return $this->_session;
        }
        $this->_session = Mage::getSingleton('centinel/session');
        return $this->_session;
    }

    /**
     * Setter for data stored in session
     *
     * @param string|array $key
     * @param string $value
     * @return Mage_Centinel_Model_Service
     */
    protected function _setDataStoredInSession($key, $value = null)
    {
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
        $key = $this->_paymentMethodCode . '_' . $key;
        return $this->_getSession()->getData($key);
    }

    /**
     * Set skip validation mode
     *
     * @param bool $shouldSkip
     * @return Mage_Centinel_Model_Service
     */
    public function skipValidation($shouldSkip = true)
    {
        $this->_skipValidation = $shouldSkip;
        return $this;
    }

    /**
     * Generate checksum from all passed parameters
     *
     * @param string $cardNumber
     * @param string $cardExpMonth
     * @param string $cardExpYear
     * @param double $amount
     * @param string $currencyCode
     * @return string
     */
    protected function _generateChecksum($cardNumber, $cardExpMonth, $cardExpYear, $amount, $currencyCode)
    {
        return md5(implode(func_get_args(), '_'));
    }

    /**
     * Unified validation/authentication URL getter
     *
     * @param string $suffix
     * @param bool $current
     * @return string
     */
    private function _getUrl($suffix, $current = false)
    {
        $request = (Mage::app()->getStore()->isAdmin() ? '*/centinel_index/' : 'centinel/index/') . $suffix;
        return Mage::getUrl($request, array(
            '_secure'  => true,
            '_current' => $current,
            'form_key' => Mage::getSingleton('core/session')->getFormKey(),
            'method'   => $this->getPaymentMethodCode())
        );
    }

    /**
     * Return URL for authentication complete response from Centinel
     *
     * @return string
     */
    public function getAuthenticationCompleteUrl()
    {
        return $this->_getUrl('authenticationcomplete', true);
    }

    /**
     * Return URL for authentication
     *
     * @return string
     */
    public function getAuthenticationStartUrl()
    {
        return $this->_getUrl('authenticationstart');
    }

    /**
     * Return URL for validation
     *
     * @return string
     */
    public function getValidatePaymentDataUrl()
    {
        return $this->_getUrl('validatepaymentdata');
    }

    /**
     * Return cmpi map
     *
     * @return array
     */
    public function getCmpiMap()
    {
        return $this->_cmpiMap;
    }

     /**
     * Export cmpi lookups and authentication information stored in session into array
     *
     * @param mixed $to
     * @param array $map
     * @return mixed $to
     */
    public function exportCmpiData($to, $map = false)
    {
        if (!$map) {
            $map = $this->getCmpiMap();
        }

        $data = array();
        $lookupData = $this->getCmpiLookupResultData();
        if ($lookupData && isset($lookupData[self::CMPI_FIELD_ENROLLED])) {
            $data = Varien_Object_Mapper::accumulateByMap($lookupData, $data, array_keys($lookupData));
            $authenticateData = $this->getCmpiAuthenticateResultData();
            if ('Y' === $lookupData[self::CMPI_FIELD_ENROLLED] && $authenticateData) {
                $data = Varien_Object_Mapper::accumulateByMap($authenticateData, $data, array_keys($authenticateData));
            }
        }
        return Varien_Object_Mapper::accumulateByMap($data, $to, $map);
    }

    /**
     * Return flag - is authentication allow
     *
     * @return bool
     */
    public function isAuthenticationAllow()
    {
        return $this->getStatus() == self::STATE_VALIDATION_SUCCESSFUL;
    }

    /**
     * Return flag - is authentication success
     *
     * @return bool
     */
    public function isAuthenticationSuccess()
    {
        if ($this->getStatus() == self::STATE_AUTENTICATION_SUCCESSFUL ||
            ($this->getStatus() == self::STATE_AUTENTICATION_FAILED && !$this->getIsAuthenticationRequired())) {
            return true;
        }
        return false;
    }

    /**
     * Payment code setter
     *
     * @param string $value
     * @return Mage_Centinel_Model_Service
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
        $api = $this->_getApi();
        $api->setCardNumber($data->getCardNumber())
            ->setCardExpMonth($data->getCardExpMonth())
            ->setCardExpYear($data->getCardExpYear())
            ->setAmount($data->getAmount())
            ->setCurrencyCode($data->getCurrencyCode())
            ->setOrderNumber($data->getOrderNumber())
            ->callLookup();

        $newChecksum = $this->_generateChecksum(
            $data->getCardNumber(), 
            $data->getCardExpMonth(), 
            $data->getCardExpYear(),
            $data->getAmount(), 
            $data->getCurrencyCode()
        );

        $this->setChecksum($newChecksum);
        
        if (!$api->getErrorNo()) {
            $this->setCmpiLookupResultData($api);
            if ($api->getEnrolled() == 'Y' && $api->getAcsUrl()) {
                $this->setStatus(self::STATE_VALIDATION_SUCCESSFUL)
                    ->setAcsUrl($api->getAcsUrl())
                    ->setPayload($api->getPayload())
                    ->setTransactionId($api->getTransactionId());
                return true;
            }
        }
        $this->setStatus(self::STATE_VALIDATION_FAILED);
        return false;
    }

    /**
     * Process authenticate validation
     *
     * @param string $PaResPayload
     * @param string $MD
     * @return bool
     */
    public function authenticate($paResPayload, $MD)
    {
        $api = $this->_getApi();
        $api->setPaResPayload($paResPayload)
            ->setTransactionId($MD)
            ->callAuthentication();

        if ($api->getErrorNo() == 0 && $api->getSignature() == 'Y' && $api->getPaResStatus() != 'N') {
            $this->setStatus(self::STATE_AUTENTICATION_SUCCESSFUL)
                ->setCmpiAuthenticateResultData($api);
            return true;
        }

        $this->setStatus(self::STATE_AUTENTICATION_FAILED);
        return false;
    }

    /**
     * Validate payment data
     *
     * @param Varien_Object $data
     * @return bool
     * @throws null
     */
    public function validate($data)
    {
        if ($this->_skipValidation) {
            return;    
        }
        
        $currentStatus = $this->getStatus(); 
        $newChecksum = $this->_generateChecksum(
            $data->getCardNumber(), 
            $data->getCardExpMonth(), 
            $data->getCardExpYear(),
            $data->getAmount(), 
            $data->getCurrencyCode()
        );

        if ($this->getIsPlaceOrder()) {
            if ($this->getChecksum() != $newChecksum) {
                Mage::throwException(Mage::helper('centinel')->__('Centinel validation is filed. Please check payment information and try again'));
            }
            if (($currentStatus == self::STATE_AUTENTICATION_SUCCESSFUL) ||
                ($currentStatus == self::STATE_VALIDATION_FAILED && !$this->getIsValidationRequired()) ||
                ($currentStatus == self::STATE_AUTENTICATION_FAILED && !$this->getIsAuthenticationRequired())) {
                return;
            }
            Mage::throwException(Mage::helper('centinel')->__('Centinel validation is not complete'));
        }

        if ($this->getChecksum() != $newChecksum) {
            $this->reset();
            $this->lookup($data);
        } 

        if ($this->getStatus() == self::STATE_VALIDATION_SUCCESSFUL) {
            return;
        }

        if ($this->getStatus() == self::STATE_VALIDATION_FAILED && $this->getIsValidationRequired()) {
            Mage::throwException(Mage::helper('centinel')->__('Centinel validation is filed. Please check payment information and try again'));
        }
    }

    /**
     * Reset data, api and state
     *
     * @return Mage_Centinel_Model_Service
     */
    public function reset()
    {
        $this->_getSession()->setData(array());
        $this->_api = null;
        $this->setStatus(self::STATE_NEED_VALIDATION);
        return $this;
    }

    /**
     * Save in session authenticate result`s fields
     *
     * @param Mage_Centinel_Model_Api $api
     * @return Mage_Centinel_Model_Service
     */
    public function setCmpiAuthenticateResultData($api)
    {
        $data = Varien_Object_Mapper::accumulateByMap($api, array(), array(
            self::CMPI_FIELD_ECI, 
            self::CMPI_FIELD_PARES, 
            self::CMPI_FIELD_XID, 
            self::CMPI_FIELD_CAVV)
        );
        $this->_setDataStoredInSession('cmpi_authenticate_result_data', $data); 
        return $this;
    } 

    /**
     * Return authenticate result`s fields 
     *         * @return array
     */
    public function getCmpiAuthenticateResultData()
    {
        return $this->_getDataStoredInSession('cmpi_authenticate_result_data');
    } 

    /**
     * Save in session lookup result`s fields
     *
     * @param Mage_Centinel_Model_Api $api
     * @return Mage_Centinel_Model_Service
     */
    public function setCmpiLookupResultData($api)
    {
        $this->_setDataStoredInSession('cmpi_lookup_result_data', array(
            self::CMPI_FIELD_ECI => $api->getEciFlag(),
            self::CMPI_FIELD_ENROLLED => $api->getEnrolled(),
        ));
        return $this;
    } 

    /**
     * Return lookup result`s fields 
     *
     * @return array
     */
    public function getCmpiLookupResultData()
    {
        return $this->_getDataStoredInSession('cmpi_lookup_result_data');
    } 

    /**
     * Setter for checksum
     *
     * @param string $value
     * @return Mage_Centinel_Model_Service
     */
    public function setChecksum($value)
    {
        return $this->_setDataStoredInSession('checksum', $value);
    }

    /**
     * Getter for checksum
     *
     * @return string
     */
    public function getChecksum()
    {
        return $this->_getDataStoredInSession('checksum');
    }

    /**
     * Setter for status
     *
     * @param string $value
     * @return Mage_Centinel_Model_Service
     */
    public function setStatus($value){
        return $this->_setDataStoredInSession('status', $value);
    }

    /**
     * Getter for status
     *
     * @return string
     */
    public function getStatus()
    {
        if ($this->_getDataStoredInSession('status')) {
            return $this->_getDataStoredInSession('status');
        }
        return self::STATE_NEED_VALIDATION;
    }

    /**
     * Setter for AcsUrl
     *
     * @param string $value
     * @return Mage_Centinel_Model_Service
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
     * @return Mage_Centinel_Model_Service
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
     * @return Mage_Centinel_Model_Service
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
}
