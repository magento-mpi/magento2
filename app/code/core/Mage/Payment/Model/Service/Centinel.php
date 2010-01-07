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
    const STATE_NO_VALIDATION           = 'no_validation';
    const STATE_VALIDATION_NOT_ENROLLED = 'not_enrolled';
    const STATE_VALIDATION_ENROLLED     = 'enrolled';
    const STATE_AUTENTICATION_COMPLETE  = 'complete';
    const STATE_AUTENTICATION_FAILED    = 'failed';

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
     * @param string|array $key
     * @param string $value
     * @return Mage_Payment_Model_Service_Centinel
     */
    protected function _setDataStoredInSession($key, $value = null)
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
     * Generate checksum from all passed parameters
     *
     * @return string
     */
    protected function _generateChecksum()
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
        $request = (Mage::app()->getStore()->isAdmin() ? '*/payment_centinel/' : 'payment/centinel/') . $suffix;
        return Mage::getUrl($request, array(
            '_secure'  => true,
            '_current' => $current,
            'form_key' => Mage::getSingleton('core/session')->getFormKey(),
            'method'   => $this->getPaymentMethodCode())
        );
    }

    /**
     * Export cmpi lookups information stored in session into array
     *
     * @param mixed $to
     * @param array $map
     * @return mixed $to
     */
    public function exportCmpi($to, array $map)
    {
        // collect available data intersected by requested map
        $data = array();
        $cmpiLookup = $this->_getDataStoredInSession('cmpi_lookup');
        if ($cmpiLookup && isset($cmpiLookup['enrolled'])) {
            $data = Varien_Object_Mapper::accumulateByMap($cmpiLookup, $data, array_keys($cmpiLookup));
            if ('Y' === $cmpiLookup['enrolled'] && $cmpiAuth = $this->_getDataStoredInSession('cmpi_authenticate')) {
                $data = Varien_Object_Mapper::accumulateByMap($cmpiAuth, $data, array_keys($cmpiAuth));
            }
        }
        return Varien_Object_Mapper::accumulateByMap($data, $to, $map);
    }

    /**
     * Return URL for term response from Centinel
     *
     * @return string
     */
    public function getTermUrl()
    {
        return $this->_getUrl('authenticate', true);
    }

    /**
     * Return URL for Centinel validation request
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->_getUrl('validate');
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
        $api = $this->_getApi();
        $api->setCardNumber($data->getCardNumber())
            ->setCardExpMonth($data->getCardExpMonth())
            ->setCardExpYear($data->getCardExpYear())
            ->setAmount($data->getAmount())
            ->setCurrencyCode($data->getCurrencyCode())
            ->setOrderNumber($data->getOrderNumber())
            ->callLookup();

        if (!$api->getErrorNo()) {
            $this->_setDataStoredInSession('cmpi_lookup', array(
                'eci_flag' => $api->getEciFlag(),
                'enrolled' => $api->getEnrolled(),
            ));
            if ($api->getEnrolled() == 'Y' && $api->getAcsUrl()) {
                $this->setAuthenticationStatus(self::STATE_VALIDATION_ENROLLED)
                    ->setAcsUrl($api->getAcsUrl())
                    ->setPayload($api->getPayload())
                    ->setTransactionId($api->getTransactionId())
                    ->setEnrolledControlSum($this->_generateChecksum(
                        $data->getCardNumber(), $data->getCardExpMonth(), $data->getCardExpYear(),
                        (double)$data->getAmount(), $data->getCurrencyCode())
                    );
                return true;
            }
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
    public function authenticate($paResPayload, $MD)
    {
        $api = $this->_getApi();
        $api->setPaResPayload($paResPayload)
            ->setTransactionId($MD)
            ->callAuthentication();

        if ($api->getErrorNo() == 0 && $api->getSignature() == 'Y' && $api->getPaResStatus() != 'N') {
            $this->setAuthenticationStatus(self::STATE_AUTENTICATION_COMPLETE)
                ->_setDataStoredInSession('cmpi_authenticate', Varien_Object_Mapper::accumulateByMap($api, array(), array(
                    'eci_flag', 'pa_res_status', 'signature_verification', 'xid', 'cavv'
                )))
            ;
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
     * @throws Mage_Core_Exception
     */
    public function validate($data)
    {
        if ($this->getIsValidationLock()) {
            return true;
        }
        switch ($this->getAuthenticationStatus()) {
            case self::STATE_NO_VALIDATION:
                Mage::throwException(Mage::helper('payment')->__('Centinel validation is requered'));
            case self::STATE_VALIDATION_NOT_ENROLLED:
                if (!$this->getIsValidationRequired()) {
                    return true;
                }
                Mage::throwException(Mage::helper('payment')->__('Centinel validation is filed. Please check information and try again'));
            case self::STATE_VALIDATION_ENROLLED:
                Mage::throwException(Mage::helper('payment')->__('Centinel validation is not complete. Please finish authorization in the Bank`s interface'));
            case self::STATE_AUTENTICATION_COMPLETE:
                if ($this->getEnrolledControlSum() == $this->_generateChecksum(
                        $data->getCardNumber(), $data->getCardExpMonth(), $data->getCardExpYear(),
                        (double)$data->getAmount(), $data->getCurrencyCode())) {
                    return true;
                }
                Mage::throwException(Mage::helper('payment')->__('Centinel validation is filed. Please check information. If You change information please revalidate it'));
            case self::STATE_AUTENTICATION_FAILED:
                if (!$this->getIsAuthenticationRequired()) {
                    return true;
                }
                Mage::throwException(Mage::helper('payment')->__('Centinel validation is filed. Please check information and try again'));
        }
        return false;
    }

    /**
     * Reset data, api and state
     *
     * @return Mage_Payment_Model_Service_Centinel
     */
    public function reset()
    {
        $this->_getSession()->setData(array());
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
}
