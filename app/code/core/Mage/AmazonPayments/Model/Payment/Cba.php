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
 * @package    Mage_AmazonPayments
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_AmazonPayments_Model_Payment_Cba extends Mage_Payment_Model_Method_Abstract
{
    /**
     * Payment module of Checkout by Amazon
     * CBA - Checkout By Amazon
     */

    protected $_code  = 'amazonpayments_cba';
    protected $_formBlockType = 'amazonpayments/cba_form';
    protected $_api;

    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = false;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = false;
    protected $_canUseForMultishipping  = false;

    const ACTION_AUTHORIZE = 0;
    const ACTION_AUTHORIZE_CAPTURE = 1;
    const PAYMENT_TYPE_AUTH = 'AUTHORIZATION';

    /**
     * Return true if the method can be used at this time
     *
     * @return bool
     */
    public function isAvailable($quote=null)
    {
        return Mage::getStoreConfig('payment/amazonpayments_cba/active');
    }

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get AmazonPayments API Model
     *
     * @return Mage_AmazonPayments_Model_Api_Cba
     */
    public function getApi()
    {
        if (!$this->_api) {
            $this->_api = Mage::getSingleton('amazonpayments/api_cba');
            $this->_api->setPaymentCode($this->getCode());
        }
        return $this->_api;
    }

    /**
     * Get AmazonPayments session namespace
     *
     * @return Mage_AmazonPayments_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('amazonpayments/session');
    }

    /**
     * Retrieve redirect url
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return Mage::getUrl('amazonpayments/cba/redirect');
    }

    /**
     * Retrieve redirect to Amazon CBA url
     *
     * @return string
     */
    public function getAmazonRedirectUrl()
    {
        return $this->getApi()->getAmazonRedirectUrl();
    }

    /**
     * Getting amazonpayments_cba action url
     *
     * @return string
     */
    public function getPaymentAction()
    {
        $paymentAction = Mage::getStoreConfig('payment/amazon_cna/payment_action');
        if (!$paymentAction) {
            $paymentAction = Mage_AmazonPayments_Model_Api_Cba::PAYMENT_TYPE_AUTH;
        }
        return $paymentAction;
    }

    /**
     * Handle Callback from CBA and calculate Shipping, Taxes in case XML-based shopping cart
     *
     */
    public function handleCallback($_request)
    {
        $response = '';

        if (!empty($_request['order-calculations-request'])) {
            $xmlRequest = urldecode($_request['order-calculations-request']);

            $session = $this->getCheckout();
            $xml = $this->getApi()->handleXmlCallback($xmlRequest, $session);

            if ($this->getDebug()) {
                $debug = Mage::getModel('amazonpayments/api_debug')
                    ->setRequestBody(print_r($_request, 1))
                    ->setResponseBody(time().' - request callback')
                    ->save();
            }

            if ($xml) {
                $xmlText = $xml->asXML();
                $response .= 'order-calculations-response='.urlencode($xmlText);
                #$response .= 'order-calculations-response='.base64_encode($xmlText);

                $secretKeyID = Mage::getStoreConfig('payment/amazonpayments_cba/secretkey_id');

                $_signature = $this->getApi()->calculateSignature($xmlText, $secretKeyID);

                if ($_signature) {
                    $response .= '&Signature='.urlencode($_signature);
                    #$response .= '&Signature='.$_signature;
                }
                $response .= '&aws-access-key-id='.urlencode(Mage::getStoreConfig('payment/amazonpayments_cba/accesskey_id'));

                if ($this->getDebug()) {
                    $debug = Mage::getModel('amazonpayments/api_debug')
                        ->setResponseBody($response)
                        ->setRequestBody(time() .' - response calllback')
                        ->save();
                }
            }
        } elseif (!empty($_request['NotificationData']) && !empty($_request['NotificationType'])) {

            if ($this->getDebug()) {
                $debug = Mage::getModel('amazonpayments/api_debug')
                    ->setRequestBody(print_r($_request, 1))
                    ->setResponseBody(time().' - Notification: '. $_request['NotificationType'])
                    ->save();
            }

            switch ($_request['NotificationType']) {
                case 'NewOrderNotification':
                    $newOrderDetails = $this->getApi()->parseOrder($_request['NotificationData']);
                    $this->_createNewOrder($newOrderDetails);
                    break;
                case 'OrderReadyToShipNotification':
                    $amazonOrderDetails = $this->getApi()->parseOrder($_request['NotificationData']);
                    $this->_proccessOrder($amazonOrderDetails);
                    break;
                case 'OrderCancelledNotification':
                    $amazonOrderDetails = $this->getApi()->parseCancelOrder($_request['NotificationData']);
                    $this->_cancelOrder($amazonOrderDetails);
                    break;
                default:
                    // Unknown notification type
            }

        } else {
            if ($this->getDebug()) {
                $debug = Mage::getModel('amazonpayments/api_debug')
                    ->setRequestBody(print_r($_request, 1))
                    ->setResponseBody(time().' - error request callback')
                    ->save();
            }
        }
        return $response;
    }

    /**
     * Create new order by data from Amazon NewOrderNotification
     *
     * @param array $newOrderDetails
     */
    protected function _createNewOrder(array $newOrderDetails)
    {
        $session = $this->getCheckout();

        #$quoteId = $session->getAmazonQuoteId();

        $quoteId = $newOrderDetails['ClientRequestId'];
        $quote = Mage::getModel('sales/quote')->load($quoteId);

        $baseCurrency = $session->getQuote()->getBaseCurrencyCode();
        $currency = Mage::app()->getStore($session->getQuote()->getStoreId())->getBaseCurrency();

        $shipping = $quote->getShippingAddress();
        $billing = $quote->getBillingAddress();

        $_address = $newOrderDetails['shippingAddress'];
        $this->_address = $_address;

        $regionModel = Mage::getModel('directory/region')->loadByCode($_address['regionCode'], $_address['countryCode']);
        $_regionId = $regionModel->getId();

        $sName = explode(' ', $newOrderDetails['shippingAddress']['name']);
        $sFirstname = isset($sName[0])?$sName[0]:'';
        $sLastname = isset($sName[1])?$sName[1]:'';

        $bName = explode(' ', $newOrderDetails['buyerName']);
        $bFirstname = isset($bName[0])?$bName[0]:'';
        $bLastname = isset($bName[1])?$bName[1]:'';

        $shipping->setCountryId($_address['countryCode'])
            ->setRegion($_address['regionCode'])
            ->setRegionId($_regionId)
            ->setCity($_address['city'])
            ->setStreet($_address['street'])
            ->setPostcode($_address['postCode'])
            ->setTaxAmount($newOrderDetails['tax'])
            ->setBaseTaxAmount($newOrderDetails['tax'])
            ->setShippingAmount($newOrderDetails['shippingAmount'])
            ->setBaseShippingAmount($newOrderDetails['shippingAmount'])
            ->setShippingTaxAmount($newOrderDetails['shippingTax'])
            ->setBaseShippingTaxAmount($newOrderDetails['shippingTax'])
            ->setDiscountAmount($newOrderDetails['discount'])
            ->setBaseDiscountAmount($newOrderDetails['discount'])
            ->setSubtotal($newOrderDetails['subtotal'])
            ->setBaseSubtotal($newOrderDetails['subtotal'])
            ->setGrandTotal($newOrderDetails['total'])
            ->setBaseGrandTotal($newOrderDetails['total'])
            ->setFirstname($sFirstname)
            ->setLastname($sLastname);

        $billing->setCountryId($_address['countryCode'])
            ->setRegion($_address['regionCode'])
            ->setRegionId($_regionId)
            ->setCity($_address['city'])
            ->setStreet($_address['street'])
            ->setPostcode($_address['postCode'])
            ->setTaxAmount($newOrderDetails['tax'])
            ->setBaseTaxAmount($newOrderDetails['tax'])
            ->setShippingAmount($newOrderDetails['shippingAmount'])
            ->setBaseShippingAmount($newOrderDetails['shippingAmount'])
            ->setShippingTaxAmount($newOrderDetails['shippingTax'])
            ->setBaseShippingTaxAmount($newOrderDetails['shippingTax'])
            ->setDiscountAmount($newOrderDetails['discount'])
            ->setBaseDiscountAmount($newOrderDetails['discount'])
            ->setSubtotal($newOrderDetails['subtotal'])
            ->setBaseSubtotal($newOrderDetails['subtotal'])
            ->setGrandTotal($newOrderDetails['total'])
            ->setBaseGrandTotal($newOrderDetails['total'])
            ->setFirstname($bFirstname)
            ->setLastname($bLastname)
            ->setAmazonemail($newOrderDetails['buyerEmailAddress']);//?????????????

        $quote->setBillingAddress($billing);
        $quote->setShippingAddress($shipping);

        $quote->save();

        $billing = $quote->getBillingAddress();
        $shipping = $quote->getShippingAddress();

        $convertQuote = Mage::getModel('sales/convert_quote');
        /* @var $convertQuote Mage_Sales_Model_Convert_Quote */
        $order = Mage::getModel('sales/order');
        /* @var $order Mage_Sales_Model_Order */

        $order = $convertQuote->addressToOrder($billing);

        // add payment information to order
        $order->setBillingAddress($convertQuote->addressToOrderAddress($billing))
            ->setShippingAddress($convertQuote->addressToOrderAddress($shipping));

        $order->setShippingDescription($newOrderDetails['ShippingLevel']);
        $order->setReferenceId($newOrderDetails['amazonOrderID']);

        $order->setAmazonname($newOrderDetails['buyerName'])
            ->setAmazonemail($newOrderDetails['buyerEmailAddress']);

        $order->setPayment($convertQuote->paymentToOrderPayment($quote->getPayment()));

        $order->addData(array('amazonOrderID' => $quote->getAmazonOrderID()));

        // add items to order
        foreach ($quote->getAllItems() as $item) {
            $order->addItem($convertQuote->itemToOrderItem($item));
        }

        $order->place();

        $customer = $quote->getCustomer();
        if (isset($customer) && $customer) { // && $quote->getCheckoutMethod()=='register') {
            $order->setCustomerId($customer->getId())
                ->setCustomerEmail($customer->getEmail())
                ->setCustomerPrefix($customer->getPrefix())
                ->setCustomerFirstname($customer->getFirstname())
                ->setCustomerMiddlename($customer->getMiddlename())
                ->setCustomerLastname($customer->getLastname())
                ->setCustomerSuffix($customer->getSuffix())
                ->setCustomerGroupId($customer->getGroupId())
                ->setCustomerTaxClassId($customer->getTaxClassId());
        }

        $order->save();

        $quote->setIsActive(false);
        $quote->save();

        $orderId = $order->getIncrementId();
        $this->getCheckout()->setLastQuoteId($quote->getId());
        $this->getCheckout()->setLastSuccessQuoteId($quote->getId());
        $this->getCheckout()->setLastOrderId($order->getId());
        $this->getCheckout()->setLastRealOrderId($order->getIncrementId());

        $order->sendNewOrderEmail();
    }

    /**
     * Proccess existing order
     *
     * @param array $amazonOrderDetails
     */
    protected function _proccessOrder($amazonOrderDetails)
    {
        if ($quoteId = $newOrderDetails['ClientRequestId']) {
            if ($order = Mage::getModel('sales/order')->loadByAttribute('quote_id', $quoteId)) {
                /** @var $order Mage_Sales_Model_Order */

                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING);
                $order->setStatus('Processing');
                $order->setIsNotified(false);
                $order->save();
            }
        }
        return true;
    }

    /**
     * Cancel the order
     *
     * @param array $amazonOrderDetails
     */
    protected function _cancelOrder($amazonOrderDetails)
    {
        if ($quoteId = $newOrderDetails['ClientRequestId']) {
            if ($order = Mage::getModel('sales/order')->loadByAttribute('quote_id', $quoteId)) {
                /** @var $order Mage_Sales_Model_Order */

                $order->setState(Mage_Sales_Model_Order::STATE_CANCELED);
                $order->setStatus('Cancel');
                $order->setIsNotified(false);
                $order->save();
            }
        }
        return true;
    }

    /**
     * Return xml with error
     *
     * @param Exception $e
     * @return string
     */

    public function callbackXmlError(Exception $e)
    {
        $_xml = $this->getApi()->callbackXmlError($e);
        $secretKeyID = Mage::getStoreConfig('payment/amazonpayments_cba/secretkey_id');
        $_signature = $this->getApi()->calculateSignature($_xml->asXml(), $secretKeyID);

        $response = 'order-calculations-response='.urlencode($_xml->asXML())
                .'&Signature='.urlencode($_signature)
                .'&aws-access-key-id='.urlencode(Mage::getStoreConfig('payment/amazonpayments_cba/accesskey_id'));
        return $response;
    }

    /**
     * Prepare fields for Html-based cart signed form for CBA
     *
     * @return array
     */
    public function getCheckoutFormFields()
    {
        $secretKeyID = Mage::getStoreConfig('payment/amazonpayments_cba/secretkey_id');
        $_quote = $this->getCheckout()->getQuote();
        /**
         * @var $_quote Mage_Sales_Model_Quote
         */
        if ($_quote->getIsVirtual()) {
            $a = $_quote->getBillingAddress();
            $b = $_quote->getShippingAddress();
        } else {
            $a = $_quote->getShippingAddress();
            $b = $_quote->getBillingAddress();
        }
        //getQuoteCurrencyCode
        $currency_code = $_quote->getBaseCurrencyCode();

        $sArr = array();
        $i = 1;
        $_merchant_id = $this->getMerchantId();
        foreach ($_quote->getAllVisibleItems() as $_item) {
            $sArr = array_merge($sArr, array(
                "item_merchant_id_{$i}"         => Mage::getStoreConfig('payment/amazonpayments_cba/merchant_id'),
                "item_title_{$i}"               => $_item->getName(),
                "item_description_{$i}"         => $_item->getProduct()->getDescription(),
                "item_price_{$i}"               => $_item->getPrice(),
                "item_quantity_{$i}"            => $_item->getQty(),
                "item_sku_{$i}"                 => $_item->getSku(),
                #"item_category_{$i}"            => $_item->getProduct()->getData('category_ids'),
            ));
            #if ($_item->hasTaxAmount() && $_item->getTaxAmount()) {
            #   $sArr["item_tax_rate_{$i}"] = $_item->getTaxAmount();
            #}
            #if ($_item->hasWeight() && $_item->getWeight()) {
            #   $sArr["item_weight_{$i}"] = $_item->getWeight();
            #}
            $i++;
        }

        $sArr = array_merge($sArr, array(
            'aws_access_key_id' => Mage::getStoreConfig('payment/amazonpayments_cba/accesskey_id'),
            'currency_code'     => $currency_code,
            'form_key'          => Mage::getSingleton('core/session')->getFormKey(),
        ));

        if($this->getConfigData('payment_action')==self::PAYMENT_TYPE_AUTH){
             $sArr = array_merge($sArr, array(
                  'paymentaction' => 'authorization'
             ));
        }

        ksort($sArr);
        $rArr = array();
        foreach ($sArr as $k=>$v) {
            /** replacing & char with and. otherwise it will break the post */
            $value =  str_replace("&","and",$v);
            $rArr[$k] =  $value;
        }

        $rArr['merchant_signature'] = $this->getApi()->calculateSignature($rArr, $secretKeyID);
        unset($rArr['form_key']);

        return $rArr;
    }

    /**
     * Prepare fields for XML-based signed cart form for CBA
     *
     * @return array
     */
    public function getCheckoutXmlFormFields()
    {
        $secretKeyID = Mage::getStoreConfig('payment/amazonpayments_cba/secretkey_id');
        $_quote = $this->getCheckout()->getQuote();

        $xml = $this->getApi()->getXmlCart($_quote);

        $xmlCart = array('order-input' =>
            "type:merchant-signed-order/aws-accesskey/1;"
            ."order:".base64_encode($xml).";"
            ."signature:{$this->getApi()->calculateSignature($xml, $secretKeyID)};"
            ."aws-access-key-id:".Mage::getStoreConfig('payment/amazonpayments_cba/accesskey_id')
            );
        if ($this->getDebug()) {
            $debug = Mage::getModel('amazonpayments/api_debug')
                ->setResponseBody(print_r($xmlCart, 1)."\norder:".$xml)
                ->setRequestBody(time() .' - xml cart')
                ->save();
        }

        return $xmlCart;
    }

    /**
     * Return CBA order details in case Html-based shopping cart commited to Amazon
     *
     */
    public function returnAmazon()
    {
        $_request = Mage::app()->getRequest()->getParams();
        #$_amazonOrderId = Mage::app()->getRequest()->getParam('amznPmtsOrderIds');
        #$_quoteId = Mage::app()->getRequest()->getParam('amznPmtsReqId');

        if ($this->getDebug()) {
            $debug = Mage::getModel('amazonpayments/api_debug')
                ->setRequestBody(print_r($_request, 1))
                ->setResponseBody(time().' - success')
                ->save();
        }
    }

    /**
     * Rewrite standard logic
     *
     * @return bool
     */
    public function isInitializeNeeded()
    {
        return false;
    }

    /**
     * Get debug flag
     *
     * @return string
     */
    public function getDebug()
    {
        return Mage::getStoreConfig('payment/' . $this->getCode() . '/debug_flag');
    }
}