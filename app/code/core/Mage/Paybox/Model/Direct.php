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
 * @package    Mage_Paybox
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paybox Direct Model
 *
 * @category   Mage
 * @package    Mage_Paybox
 * @author     Ruslan Voitenko <ruslan@voytenko@varien.com>
 */
class Mage_Paybox_Model_Direct extends Mage_Payment_Model_Method_Cc
{
    const PBX_PAYMENT_ACTION_ATHORIZE = '00001';
    const PBX_PAYMENT_ACTION_DEBIT = '00002';
    const PBX_PAYMENT_ACTION_ATHORIZE_CAPTURE = '00003';
    const PBX_PAYMENT_ACTION_CANCELLATION = '00005';
    const PBX_PAYMENT_ACTION_REFUND = '00004';

    protected $_code  = 'paybox_direct';

    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = true;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc               = true;

    protected $_formBlockType = 'paybox/direct_form';
    protected $_infoBlockType = 'paybox/direct_info';

    protected $_order;
    protected $_currenciesNumbers;
    protected $_questionNumberModel;

    public function getPayboxUrl($recallNumber)
    {
        $path = 'pbx_url';
        if ($recallNumber) {
            $path = 'pbx_backupurl';
        }
        return Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/' . $path);
    }

    /**
     * Get Payment Action of Paybox System changed to Paybox specification
     *
     * @return string
     */
    public function getPaymentAction()
    {
        $paymentAction = Mage::getStoreConfig('paybox/' . $this->getCode() . '/payment_action');
        switch ($paymentAction) {
            case self::ACTION_AUTHORIZE:
                return self::PBX_PAYMENT_ACTION_ATHORIZE;
                break;
            case self::ACTION_AUTHORIZE_CAPTURE:
                return self::PBX_PAYMENT_ACTION_ATHORIZE_CAPTURE;
                break;
            default:
                return self::PBX_PAYMENT_ACTION_ATHORIZE;
                break;
        }
    }

    public function getSiteNumber()
    {
        return Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_site');
    }

    public function getRang()
    {
        return Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_rang');
    }

    public function getCleNumber()
    {
        return Mage::getStoreConfig('paybox/' . $this->getCode() . 'api/pbx_cle');
    }

    public function getCurrencyNumb()
    {
        $currencyCode = $this->getPayment()->getOrder()->getBaseCurrencyCode();
        if (!$this->_currenciesNumbers) {
            $this->_currenciesNumbers = simplexml_load_file(Mage::getBaseDir().'/app/code/core/Mage/Paybox/etc/currency.xml');
        }
        if ($this->_currenciesNumbers->$currencyCode) {
            return (string)$this->_currenciesNumbers->$currencyCode;
        }
    }

    /**
     * Enter description here...
     *
     * @return Mage_Paybox_Model_Question_Number
     */
    public function getQuestionNumberModel()
    {
        if (!$this->_questionNumberModel) {
            $accountHash = md5($this->getSiteNumber().$this->getRang());
            $this->_questionNumberModel = Mage::getModel('paybox/question_number')->load($accountHash, 'account_hash');
        }
        return $this->_questionNumberModel;
    }

    public function getDebugFlag()
    {
        return Mage::getStoreConfigFlag('paybox/' . $this->getCode() . 'api/debug_flag');
    }

    public function validate()
    {
        return parent::validate();
    }

    public function authorize(Varien_Object $payment, $amount)
    {
        parent::authorize($payment, $amount);

        $this->setAmount($amount)
            ->setPayment($payment);

        if ($this->callDoDirectPayment()!==false) {
            $payment->setStatus(self::STATUS_APPROVED)
                ->setLastTransId($this->getTransactionId())
                ->setPayboxRequestNumber($this->getRequestNumber())
                ->setPayboxQuestionNumber($this->getQuestionNumber());
        } else {
            $e = $this->getError();
            if (isset($e['message'])) {
                $message = Mage::helper('eway')->__('There has been an error processing your payment. ') . $e['message'];
            } else {
                $message = Mage::helper('eway')->__('There has been an error processing your payment. Please try later or contact us for help.');
            }
            Mage::throwException($message);
        }

        return $this;
    }

    public function capture(Varien_Object $payment, $amount)
    {
        parent::capture($payment, $amount);

        $this->setAmount($amount)
            ->setPayment($payment);

        if ($payment->getLastTransId()) {//if after authorize
            $result = $this->callDoDebitPayment()!==false;
        } else {//authorize+capture (debit)
            $result = $this->callDoDirectPayment()!==false;
        }

        if ($result) {
            $payment->setStatus(self::STATUS_APPROVED)
                ->setLastTransId($this->getTransactionId())
                ->setPayboxRequestNumber($this->getRequestNumber());
        } else {
            $e = $this->getError();
            if (isset($e['message'])) {
                $message = Mage::helper('eway')->__('There has been an error processing your payment. ') . $e['message'];
            } else {
                $message = Mage::helper('eway')->__('There has been an error processing your payment. Please try later or contact us for help.');
            }
            Mage::throwException($message);
        }

        return $this;
    }

    public function cancel(Varien_Object $payment)
    {
        $payment->setStatus(self::STATUS_DECLINED);
        return $this;
    }

    public function refund(Varien_Object $payment, $amount)
    {
        parent::refund($payment, $amount);

        $error = false;
        if($payment->getRefundTransactionId() && $amount>0) {
            $this->setTransactionId($payment->getRefundTransactionId())
                ->setPayment($payment)
                ->setAmount($amount);

            if ($this->callDoRefund()!==false) {
                $payment->setStatus(self::STATUS_SUCCESS)
                    ->setCcTransId($this->getTransactionId());
            } else {
                $payment->setStatus(self::STATUS_ERROR);
                $e = $this->getError();
                if (isset($e['message'])) {
                    $error = $e['message'];
                } else {
                    $error = Mage::helper('paybox')->__('Error in refunding the payment');
                }
            }
        } else {
            $payment->setStatus(self::STATUS_ERROR);
            $error = Mage::helper('paybox')->__('Error in refunding the payment');
        }
        if ($error !== false) {
            Mage::throwException($error);
        }

        return $this;
    }

    public function callDoDirectPayment()
    {
        $payment = $this->getPayment();
        $requestStr = '';

        $tmpArr = array(
            'VERSION' => '00103',//!!!!????
            'DATEQ' => Mage::getModel('core/date')->date('dmYHis'),//i.e ddmmyyyyhhmmss
            'TYPE' => $this->getPaymentAction(),//i.e 0000$t (types:1 = authorization, 2 = debit, 3 = authorization + debit, 4 = credit, 5 = cancellation, 11= Checking of the existence of a transaction, 12 = transaction without request for authorization, 13 = Modification of the amount of a transaction, 14 = Refund)
            'NUMQUESTION' => $this->getQuestionNumberModel()->getNextQuestionNumber(),
            'SITE' => $this->getSiteNumber(),//for TYPE 2,5,11,13
            'RANG' => $this->getRang(),//for TYPE 2,5,11,13
            'CLE' => $this->getCleNumber(),
            'IDENTIFIANT' => '',//!!!!!! empty field by doc
            'MONTANT' => ($this->getAmount()*100),//for TYPE 2,5,11
            'DEVISE' => $this->getCurrencyNumb(),//currency
            'REFERENCE' => base64_encode($payment->getOrder()->getRealOrderId()),//for all TYPEs except 13, have to be encoded
            'PORTEUR' => $payment->getCcNumber(),//for TYPE 1, 3, 4 and 12. Not checked for TYPE 5
            'DATEVAL' => Mage::getModel('core/date')->date('my', mktime(0,0,0,$payment->getCcExpMonth(),1,$payment->getCcExpYear())),//expiry date for TYPE 1, 3, 4, 5 and 12. i.e MMYY  !!!!!!!!??????????
            'CVV' => $payment->getCcCid(),
            'ACTIVITE' => '024',//!!!!! request by internet (can be by phone etc.)
//            'ARCHIVAGE' => 'AXZ130968CT2',//for charge backs
//            'DIFFERE' => '000',///days before to send transactions to bank
//            'PAYS' => '',//country code to returne in the response
//            'PRIV_CODETRAITEMENT' => '',//SOFINCO or COFINOGA :))))
//            'DATENAISS' => '08031964',//date of birth of the cardholder for the payment with COFINOGA card
        );

        foreach ($tmpArr as $param=>$value) {
            $requestStr .= $param . '=' . $value . '&';
        }
        $requestStr = substr($requestStr, 0, -1);

        $resultArr = $this->call($requestStr);

        if ($resultArr === false) {
            return false;
        }

        $this->getQuestionNumberModel()
                ->increaseQuestionNumber();

        $this->setTransactionId($resultArr['NUMTRANS']);
        $this->setRequestNumber($resultArr['NUMAPPEL']);
        $this->setQuestionNumber($resultArr['NUMQUESTION']);

        return $resultArr;
    }

    public function callDoDebitPayment()
    {
        $payment = $this->getPayment();
        $requestStr = '';

        $tmpArr = array(
            'VERSION' => '00103',
            'DATEQ' => Mage::getModel('core/date')->date('dmYHis'),
            'TYPE' => self::PBX_PAYMENT_ACTION_DEBIT,
            'NUMQUESTION' => $payment->getPayboxQuestionNumber(),
            'SITE' => $this->getSiteNumber(),
            'RANG' => $this->getRang(),
            'CLE' => $this->getCleNumber(),
            'MONTANT' => ($this->getAmount()*100),
            'DEVISE' => (string)$this->getCurrencyNumb(),
            'REFERENCE' => base64_encode($payment->getOrder()->getRealOrderId()),
            'NUMAPPEL' => $payment->getPayboxRequestNumber(),
            'NUMTRANS' => $payment->getLastTransId(),
        );

        foreach ($tmpArr as $param=>$value) {
            $requestStr .= $param . '=' . $value . '&';
        }
        $requestStr = substr($requestStr, 0, -1);

        $resultArr = $this->call($requestStr);

        if ($resultArr === false) {
            return false;
        }

        $this->setTransactionId($resultArr['NUMTRANS']);

        return $resultArr;
    }

    public function callDoVoid($payment)
    {
        $requestStr = '';

        $tmpArr = array(
            'VERSION' => '00103',
            'DATEQ' => Mage::getModel('core/date')->date('dmYHis'),
            'TYPE' => self::PBX_PAYMENT_ACTION_CANCELLATION,
            'NUMQUESTION' => $this->getQuestionNumberModel()->getNextQuestionNumber(),//'1000000031'
            'SITE' => $this->getSiteNumber(),
            'RANG' => $this->getRang(),
            'CLE' => $this->getCleNumber(),
            'MONTANT' => ($this->getAmount()*100),
            'DEVISE' => (string)$this->getCurrencyNumb(),
            'REFERENCE' => base64_encode($payment->getOrder()->getRealOrderId()),
            'DATEVAL' => Mage::getModel('core/date')->date('my', mktime(0,0,0,$payment->getCcExpMonth(),1,$payment->getCcExpYear())),
            'NUMAPPEL' => $payment->getPayboxRequestNumber(),
            'NUMTRANS' => $payment->getLastTransId(),
        );

        foreach ($tmpArr as $param=>$value) {
            $requestStr .= $param . '=' . $value . '&';
        }
        $requestStr = substr($requestStr, 0, -1);

        $resultArr = $this->call($requestStr);

        if ($resultArr === false) {
            return false;
        }

        $this->getQuestionNumberModel()
            ->increaseQuestionNumber();

        $this->setTransactionId($resultArr['NUMTRANS']);

        return $resultArr;
    }

    public function callDoRefund()
    {
        $payment = $this->getPayment();
        $requestStr = '';

        $tmpArr = array(
            'VERSION' => '00103',
            'DATEQ' => Mage::getModel('core/date')->date('dmYHis'),
            'TYPE' => self::PBX_PAYMENT_ACTION_REFUND,
            'NUMQUESTION' => $this->getQuestionNumberModel()->getNextQuestionNumber(),
            'SITE' => $this->getSiteNumber(),
            'RANG' => $this->getRang(),
            'CLE' => $this->getCleNumber(),
            'MONTANT' => ($this->getAmount()*100),
            'DEVISE' => (string)$this->getCurrencyNumb(),
            'REFERENCE' => base64_encode($payment->getOrder()->getRealOrderId()),
            'PORTEUR' => $payment->getCcNumber(),
            'DATEVAL' => Mage::getModel('core/date')->date('my', mktime(0,0,0,$payment->getCcExpMonth(),1,$payment->getCcExpYear())),
            'NUMAPPEL' => '',
            'NUMTRANS' => '',
        );

        foreach ($tmpArr as $param=>$value) {
            $requestStr .= $param . '=' . $value . '&';
        }
        $requestStr = substr($requestStr, 0, -1);

        $resultArr = $this->call($requestStr);

        if ($resultArr === false) {
            return false;
        }

        $this->getQuestionNumberModel()
            ->increaseQuestionNumber();

        $this->setTransactionId($resultArr['NUMTRANS']);

        return $resultArr;
    }

    public function call($requestStr)
    {
        if ($this->getDebugFlag()) {
            $debug = Mage::getModel('paybox/api_debug')
                ->setRequestBody($requestStr)
                ->save();
        }
        $recall = true;
        $recallCounter = 0;
        while ($recall && $recallCounter < 3) {
            $recall = false;
            $this->unsError();

            $http = new Varien_Http_Adapter_Curl();
            $config = array('timeout' => 30);
            $http->setConfig($config);
            $http->write(Zend_Http_Client::POST, $this->getPayboxUrl($recallCounter), '1.1', array(), $requestStr);
            $response = $http->read();

            $response = preg_split('/^\r?$/m', $response, 2);
            $response = trim($response[1]);

            if ($http->getErrno()) {
                $http->close();
                if ($this->getDebugFlag()) {
                    $debug->setResponseBody($response)->save();
                }
                $this->setError(array(
                    'message' => $http->getError()
                ));
                return false;
            }
            $http->close();

            $parsedResArr = $this->parseResponseStr($response);

            if ($parsedResArr['CODEREPONSE'] == '00001' ||
                $parsedResArr['CODEREPONSE'] == '00097' ||
                $parsedResArr['CODEREPONSE'] == '00098'
                ) {
                $recallCounter++;
                $recall = true;
            }
        }

        if ($this->getDebugFlag()) {
            $debug->setResponseBody($response)->save();
        }

        if ($recall) {
            $this->setError(array(
                'message' => Mage::helper('paybox')->__('Paybox payment gateway is not available right now')
            ));
            return false;
        }

        if ($parsedResArr['CODEREPONSE'] == '00000') {
                return $parsedResArr;
        }

        if (isset($parsedResArr['COMMENTAIRE'])) {
            $this->setError(array(
                'message' => $parsedResArr['CODEREPONSE'] . ':' . $parsedResArr['COMMENTAIRE']
            ));
        }

        return false;
    }

    public function parseResponseStr($str)
    {
        $tmpResponseArr = explode('&', $str);
        $responseArr = array();
        foreach ($tmpResponseArr as $response) {
            $paramValue = explode('=', $response);
            $responseArr[$paramValue[0]] = $paramValue[1];
        }

        return $responseArr;
    }
}