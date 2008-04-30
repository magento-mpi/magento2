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
 * @category   Mage
 * @package    Mage_Chronopay
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * ChronoPay Gateway Model
 *
 * @category   Mage
 * @package    Mage_Chronopay
 * @name       Mage_Chronopay_Model_Gateway
 * @author	   Magento Core Team <core@magentocommerce.com>
 */

class Mage_Chronopay_Model_Gateway extends Mage_Payment_Model_Method_Cc
{
    const CGI_URL = 'https://secure.chronopay.com/gateway.cgi';

    const OPCODE_CHARGING               = 1;
    const OPCODE_REFUND                 = 2;
    const OPCODE_AUTHORIZE              = 4;
    const OPCODE_VOID_AUTHORIZE         = 5;
    const OPCODE_CONFIRM_AUTHORIZE      = 6;
    const OPCODE_CUSTOMER_FUND_TRANSFER = 8;

    protected $_code  = 'chronopay_gateway';

    protected $_formBlockType = 'chronopay/form';
//    protected $_infoBlockType = 'chronopay/info';
    /**
     * Availability options
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc               = false;

    public function getConfigData($key, $default=false)
    {
        if (!$this->hasData($key)) {
             $value = Mage::getStoreConfig('payment/chronopay_gateway/'.$key);
             if (is_null($value) || false===$value) {
                 $value = $default;
             }
            $this->setData($key, $value);
        }
        return $this->getData($key);
    }

    /**
     *  Description goes here...
     *
     *  @param    none
     *  @return	  void
     */
    protected function _getTestResponseBody ($request)
    {
        $error = "\nN|Incorrect hash";
        $success = "\nY|".($request->getOpcode() == 5 || $request->getOpcode() == 6 ? 'Completed' : $request->getTransaction());
        if ($request->setShowTransactionId()) {
            $success = "\nT|" . $request->getTransaction() . "|" . $success;
        }
        return $success;
//        return $error;
    }

    /**
     *
     *
     *  @param    none
     *  @return	  void
     */
    protected function _getIp ()
    {
//        return '222.244.15.13';
        return '82.144.205.117';
        //$order->getRemoteIp()
    }

    /**
     *  Description goes here...
     *
     *  @param    none
     *  @return	  void
     */
    protected function _getSharedSecret ()
    {
        return 'djihwfdu7gebdchb';
//        return 'chronopay_hgdbjku';
//        return 'magento123';
    }

    /**
     * Send authorize request to gateway
     *
     * @param   Varien_Object $payment
     * @param   decimal $amount
     * @return  Mage_Paygate_Model_Authorizenet
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        $payment->setAmount($amount);
        $payment->setOpcode(self::OPCODE_AUTHORIZE);
        $payment->setTransaction($payment->getOrder()->getRealOrderId());

        $request = $this->_buildRequest($payment);
        $result = $this->_postRequest($request);

        if (!$result->getError()) {
            $payment->setStatus(self::STATUS_APPROVED);
            $payment->setLastTransId($payment->getTransaction());
            $payment->setCcTransId($payment->getTransaction());
        } else {
            Mage::throwException($result->getError());
        }

        return $this;
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setAmount($amount);
        $payment->setOpcode(self::OPCODE_CHARGING);
        $payment->setTransaction($payment->getOrder()->getRealOrderId());

        $request = $this->_buildRequest($payment);
        $result = $this->_postRequest($request);

        if (!$result->getError()) {
            $payment->setStatus(self::STATUS_APPROVED);
            $payment->setLastTransId($payment->getTransaction());
        } else {
            Mage::throwException($result->getError());
        }

        return $this;
    }

    /**
     *  Description goes here...
     *
     *  @param    none
     *  @return	  void
     */
    public function void (Varien_Object $payment)
    {
        $payment->setOpcode(self::OPCODE_VOID_AUTHORIZE);
        $payment->setTransaction($payment->getVoidTransactionId());

        $request = $this->_buildRequest($payment);
        return $this;
        $result = $this->_postRequest($request);

        $payment->setStatus(self::STATUS_APPROVED);
        $payment->setLastTransId($result->getTransactionId());

        return $this;
    }

    /**
     *  Description goes here...
     *
     *  @param    none
     *  @return	  void
     */
    public function refund (Varien_Object $payment, $amount)
    {
        $payment->setAmount($amount);
        $payment->setOpcode(self::OPCODE_REFUND);
        $payment->setTransaction($payment->getRefundTransactionId());

        $request = $this->_buildRequest($payment);
        return $this;
        $result = $this->_postRequest($request);

        $payment->setStatus(self::STATUS_APPROVED);
        $payment->setLastTransId($result->getTransactionId());

        return $this;
    }

    /**
     *  Description goes here...
     *
     *  @param    none
     *  @return	  void
     */
    protected function _buildRequest (Varien_Object $payment)
    {
        $order = $payment->getOrder();
        $billing = $order->getBillingAddress();
        $streets = $billing->getStreet();
        $street = isset($streets[0]) && $streets[0] != ''
                  ? $streets[0]
                  : (isset($streets[1]) && $streets[1] != '' ? $streets[1] : '');


        $request = Mage::getModel('chronopay/gateway_request')
            ->setOpcode($payment->getOpcode())
            ->setProductId($this->getConfigData('product_id'))
            ->setTransaction($payment->getTransaction())
            ->setCustomer($order->getCustomerId())
            ->setFname($billing->getFirstname())
            ->setLname($billing->getLastname())
            ->setCardholder($payment->getCcOwner())
            ->setZip($billing->getPostcode())
            ->setStreet($street)
            ->setCity($billing->getCity())
            ->setState($billing->getRegionModel()->getCode())
            ->setCountry($billing->getCountryModel()->getIso3Code())
            ->setEmail($order->getCustomerEmail())
            ->setPhone($billing->getTelephone())
            ->setIp($this->_getIp())
            ->setCardNo($payment->getCcNumber())
            ->setCvv($payment->getCcCid())
            ->setExpirey($payment->getCcExpYear())
            ->setExpirem($payment->getCcExpMonth())
            ->setAmount($payment->getAmount())
            ->setCurrency($order->getBaseCurrencyCode())
            ->setShowTransactionId(1);

        $hash = $this->_getHash($request);
        $request->setHash($hash);
        return $request;
    }

    /**
     *  Description goes here...
     *
     *  @param    none
     *  @return	  void
     */
    protected function _postRequest (Mage_Chronopay_Model_Gateway_Request $request)
    {
        $result = Mage::getModel('chronopay/gateway_result');

        $client = new Varien_Http_Client();

        $url = $this->getConfigData('cgi_url');
        $client->setUri($url ? $url : self::CGI_URL);
        $client->setConfig(array(
            'maxredirects' => 0,
            'timeout' => 30,
        ));
        $client->setParameterPost($request->getData());
        $client->setMethod(Zend_Http_Client::POST);

        if ($this->getConfigData('debug_flag')) {
            $debug = Mage::getModel('chronopay/api_debug')
                ->setRequestBody($client->getUri() . "\n" . print_r($request->getData(), 1))
                ->save();
        }

        try {
            $response = $client->request();
            $body = $response->getRawBody();
//            $body = $this->_getTestResponseBody($request);
            if (preg_match('/^[\r\n]+(T\|([^\|]+)\|[\r\n]+)?([YN])\|(.+)$/', $body, $matches)) {

                $transactionId = $matches[2];
                $status = $matches[3];
                $message = $matches[4];

                if ($status == 'N') {
                    $result->setError($message);
                    throw new Exception($message);
                }

                if ($request->getShowTransactionId()) {
                    $result->setTransaction($transactionId);
                } elseif ($message != 'Completed') {
                    $result->setTransaction($message);
                }
                if ($result->getTransaction() && $request->getTransaction() != $result->getTransaction()) {
                    throw new Exception('Transaction ID is invalid');
                }

                $result->setCompleted($message == 'Completed');

            } else {
                throw new Exception('Invalid response format');
            }

            if ($this->getConfigData('debug_flag')) {
                $debug->setResponseBody($body)->save();
            }

        } catch (Exception $e) {
            $result->setResponseCode(-1)
                ->setResponseReasonCode($e->getCode())
                ->setResponseReasonText($e->getMessage());


            $exceptionMsg = Mage::helper('chronopay')->__('Gateway request error: %s', $e->getMessage());

            if ($this->getConfigData('debug_flag')) {
                $debug->setResponseBody($exceptionMsg)->save();
            }

            Mage::throwException($exceptionMsg);
        }
        return $result;
    }

    /**
     *  Generate MD5 hash for transaction checksum
     *
     *  @param    Mage_Chronopay_Model_Gateway_Request
     *  @return	  string MD5
     */
    protected function _getHash (Mage_Chronopay_Model_Gateway_Request $request)
    {
        $result = sprintf('%s + %s + %s',
            $this->_getSharedSecret(),
            $request->getOpcode(),
            $request->getProductId()
        );

        switch ($request->getOpcode()) {
            case self::OPCODE_CHARGING :
            case self::OPCODE_AUTHORIZE :
                $result .= ' + ' . sprintf('%s + %s + %s + %s + %s + %s',
                    $request->getFname(),
                    $request->getLname(),
                    $request->getStreet(),
                    $this->_getIp(),
                    $request->getCardNo(),
                    $request->getAmount()
                );
                break;

            case self::OPCODE_REFUND :
            case self::OPCODE_VOID_AUTHORIZE :
            case self::OPCODE_CONFIRM_AUTHORIZE :
                $result .= ' + ' . sprintf('%s', $request->getTransaction());
                break;

            case self::OPCODE_CUSTOMER_FUND_TRANSFER :
                $result .= ' + ' . sprintf('%s + %s + %s',
                    $request->getCustomer(),
                    $request->getTransaction(),
                    $request->getAmount()
                );
                break;

            default :
                Mage::throwException(
                    Mage::helper('chronopay')->__('Invalid operation code')
                );
                break;
        }
        return md5($result);
    }

}
