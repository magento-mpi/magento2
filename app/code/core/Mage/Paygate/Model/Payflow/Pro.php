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
 * @package    Mage_Paygate
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payflow Pro payment gateway model
 *
 * @category   Mage
 * @package    Mage_Paygate
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Paygate_Model_Payflow_Pro extends Mage_Payment_Model_Cc
{
    const TRXTYPE_AUTH_ONLY         = 'A';
    const TRXTYPE_SALE              = 'S';
    const TRXTYPE_CREDIT            = 'C';
    const TRXTYPE_DELAYED_CAPTURE   = 'D';
    const TRXTYPE_DELAYED_VOID      = 'V';
    const TRXTYPE_DELAYED_VOICE     = 'F';
    const TRXTYPE_DELAYED_INQUIRY   = 'I';

    const TENDER_AUTOMATED          = 'A';
    const TENDER_CC                 = 'C';
    const TENDER_PINLESS_DEBIT      = 'D';
    const TENDER_ECHEK              = 'E';
    const TENDER_TELECHECK          = 'K';
    const TENDER_PAYPAL             = 'P';

    const RESPONSE_DELIM_CHAR = ',';

    const RESPONSE_CODE_APPROVED = 0;
    const RESPONSE_CODE_DECLINED = 12;

    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('payment/form_cc', $name)
            ->setMethod('verisign')
            ->setPayment($this->getPayment());
        return $block;
    }

    public function createInfoBlock($name)
    {
        $block = $this->getLayout()->createBlock('payment/info_cc', $name)
            ->setPayment($this->getPayment());
        return $block;
    }

    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
        $payment->setTrxtype(self::TRXTYPE_AUTH_ONLY);
        $payment->setDocument($payment->getOrder());

        $request = $this->buildRequest($payment);
        $result = $this->postRequest($request);

        $payment->setCcTransId($result->getPnref());

        if (Mage::getStoreConfig('payment/verisign/debug')) {
            $payment->setCcDebugRequestBody($result->getRequestBody())
                ->setCcDebugResponseSerialized(serialize($result));
        }

        switch ($result->getResultCode()) {
            case self::RESPONSE_CODE_APPROVED:
                $payment->setStatus('APPROVED');
                $payment->getOrder()->addStatus(Mage::getStoreConfig('payment/verisign/order_status'));
                break;

            case self::RESPONSE_CODE_DECLINED:
                $payment->setStatus('DECLINED');
                $payment->setStatusDescription($result->getRespmsg());
                break;

            default:
                $payment->setStatus('UNKNOWN');
                $payment->setStatusDescription($result->getRespmsg());
                break;
        }

        return $this;
    }

    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Payment $payment)
    {
        $payment->setDocument($payment->getInvoice());

        foreach ($order->getAllPayments() as $transaction) {
            break;
        }

        if ($transaction->setCcTransId()) {
            $transaction->setTrxtype(self::TRXTYPE_DELAYED_CAPTURE);
        }
        $request = $this->buildRequest($transaction);
        #$result = $this->postRequest($request);
    }

    public function postRequest(Varien_Object $request)
    {
        foreach( $request->getData() as $key => $value ) {
            $requestData[] = strtoupper($key) . '=' . $value;
        }

        $requestData = join('&', $requestData);

        $client = new Varien_Http_Client();

        $uri = Mage::getStoreConfig('payment/verisign/url');
        $client->setUri($uri)
               ->setConfig(array(
                    'maxredirects'=>5,
                    'timeout'=>30,
                ))
            ->setMethod(Zend_Http_Client::POST)
            ->setParameterPost($request->getData())
            ->setHeaders('X-VPS-VIT-CLIENT-CERTIFICATION-ID: 33baf5893fc2123d8b191d2d011b7fdc')
            ->setHeaders('X-VPS-Request-ID: ' . $request->getRequestId())
        ;

        $response = $client->request();
        $result = Mage::getModel('paygate/payflow_pro_result');

        $response = strstr($response->getBody(), 'RESULT');
        $valArray = explode('&', $response);
        foreach($valArray as $val) {
        		$valArray2 = explode('=', $val);
        		$result->setData(strtolower($valArray2[0]), $valArray2[1]);
        }

        $result->setResultCode($result->getResult())
            ->setRespmsg($result->getRespmsg);

        return $result;
    }

    public function buildRequest(Varien_Object $payment)
    {
        $document = $payment->getDocument();

        if( !$payment->getTrxtype() ) {
            $payment->setTrxtype(self::TRXTYPE_AUTH_ONLY);
        }

        if( !$payment->getTender() ) {
            $payment->setTender(self::TENDER_CC);
        }

        $request = Mage::getModel('paygate/payflow_pro_request')
            ->setUser(Mage::getStoreConfig('payment/verisign/user'))
            ->setVendor(Mage::getStoreConfig('payment/verisign/vendor'))
            ->setPartner(Mage::getStoreConfig('payment/verisign/partner'))
            ->setPwd(Mage::getStoreConfig('payment/verisign/pwd'))
            ->setTender($payment->getTender())
            ->setTrxtype($payment->getTrxtype())
            ->setVerbosity(Mage::getStoreConfig('payment/verisign/verbosity'))
            ->setAmt(round($payment->getAmount(), 2))
            ->setRequestId($this->_generateRequestId())
            ;

        switch ($request->getTender()) {
            case self::TENDER_CC:
                $request->setAcct($payment->getCcNumber())
                    ->setExpdate(sprintf('%02d%04d', $payment->getCcExpMonth(), $payment->getCcExpYear()))
                    ->setCvv2($payment->getCcCid());
                break;
        }
        return $request;
    }

    protected function _generateRequestId()
    {
        return md5(microtime() . rand(0, time()));
    }
}