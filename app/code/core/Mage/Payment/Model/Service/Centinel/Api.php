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
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * 3D Secure Validation Library for Payment
 */
include_once '3Dsecure/CentinelClient.php';

/**
 * 3D Secure Validation Api
 */
class Mage_Payment_Model_Service_Centinel_Api extends Mage_Core_Model_Abstract
{
    /**
     * Centinel validation client
     *
     * @var CentinelClient
     */
protected $_thinClient = null;

    /**
     * Return Centinel thin client object
     *
     * @return CentinelClient
     */
    protected function _getThinClient()
    {
        if (empty($this->_thinClient)) {
            $this->_thinClient = new CentinelClient();
        }
        return $this->_thinClient;
    }

    /**
     * Return Centinel Api version
     *
     * @return string
     */
    protected function _getVersion()
    {
        return '1.7';
    }

    /**
     * Return transaction type. according centinel documetation it should be "C"
     *
     * @return "C"
     */
    protected function _getTransactionType()
    {
        return 'C';
    }

    /**
     * Call centinel api methods by given method name and data
     *
     * @param $method string
     * @param $data array
     *
     * @return CentinelClient
     */
    protected function _call($method, $data)
    {
        $thinData = array(
            'MsgType'           => $method,
            'Version'           => $this->_getVersion(),
            'ProcessorId'       => $this->getProcessorId(),
            'MerchantId'        => $this->getMerchantId(),
            'TransactionPwd'    => $this->getTransactionPwd(),
            'TransactionType'   => $this->_getTransactionType(),
        );
        
        $thinClient = $this->_getThinClient();
        $thinData = array_merge($thinData, $data);
        if (count($thinData) > 0) {
        foreach($thinData AS $key => $val) {
            $thinClient->add($key, $val);
            }
        }

        $thinClient->sendHttp($this->getMapUrl(), $this->getTimeoutConnect(), $this->getTimeoutRead());
        return $thinClient;
    }

    /**
     * Call centinel api lookup method
     *
     * @return Mage_Payment_Model_Service_Centinel_Api
     */
    public function callLookup()
    {
        $month = strlen($this->getCardExpMonth()) == 1 ? '0' . $this->getCardExpMonth() : $this->getCardExpMonth();
        $currencyIso = '';
        try {
            $currencyIso = Mage::getModel('payment/service_centinel_api_currency')->getIso4217CurrencyCode($this->getCurrencyCode());
        }catch (Mage_Core_Exception $e) {
            $this->setErrorNo(1);
            $this->setErrorDesc($e);
            return $this;
        }

        $lookUpArray = array(
            'Amount' => round($this->getAmount() * 100),
            'CurrencyCode' => $currencyIso,
            'CardNumber' =>  $this->getCardNumber(),
            'CardExpMonth'=> $month,
            'CardExpYear' =>  $this->getCardExpYear(),
            'OrderNumber' => $this->getOrderNumber()
        );

        $clientResponse = $this->_call('cmpi_lookup', $lookUpArray);

        $this->setEnrolled($clientResponse->getValue('Enrolled'));
        $this->setErrorNo($clientResponse->getValue('ErrorNo'));
        $this->setErrorDesc($clientResponse->getValue('ErrorDesc'));
        $this->setEciFlag($clientResponse->getValue('EciFlag'));
        $this->setAcsUrl($clientResponse->getValue('ACSUrl'));
        $this->setPayload($clientResponse->getValue('Payload'));
        $this->setOrderId($clientResponse->getValue('OrderId'));
        $this->setTransactionId($clientResponse->getValue('TransactionId'));
        $this->setAuthenticationPath($clientResponse->getValue('AuthenticationPath'));
        $this->setTermUrl($this->getTermUrl());

        return $this;
    }

    /**
     * Call centinel api authentication method
     *
     * @return Mage_Payment_Model_Service_Centinel_Api
     */
    public function callAuthentication()
    {
        $authArray = array(
            'TransactionId' => $this->getTransactionId(),
            'PAResPayload'  => $this->getPaResPayload(),
        );

        $clientResponse = $this->_call('cmpi_authenticate', $authArray);

        $this->setErrorNo($clientResponse->getValue('ErrorNo'));
        $this->setErrorDesc($clientResponse->getValue('ErrorDesc'));
        $this->setPaResStatus($clientResponse->getValue('PAResStatus'));
        $this->setCavv($clientResponse->getValue('Cavv'));
        $this->setSignature($clientResponse->getValue('SignatureVerification'));
        $this->setEciFlag($clientResponse->getValue('EciFlag'));
        $this->setXid($clientResponse->getValue('Xid'));
        return $this;
    }
}
