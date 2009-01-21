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

class Mage_AmazonPayments_Model_Payment_CBA extends Mage_Payment_Model_Method_Abstract
{
    /**
     * Payment module of Checkout by Amazon
     * CBA - Checkout By Amazon
     */

    protected $_code  = 'amazon_cba';
    protected $_formBlockType = 'amazonpayments/cba_form';
    protected $_api;

    const ACTION_AUTHORIZE = 0;
    const ACTION_AUTHORIZE_CAPTURE = 1;
    const PAYMENT_TYPE_AUTH = 'AUTHORIZATION';

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
     * @return Mage_Paypal_Model_Api_Nvp
     */
    public function getApi()
    {
        if (!$this->_api) {
            $this->_api = Mage::getSingleton('amazonpayments/api');
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

    public function getOrderPlaceRedirectUrl()
    {
        echo "getOrderPlaceRedirectUrl\n";
        return false;
    }

    public function getCheckoutRedirectUrl()
    {
        #echo "getCheckoutRedirectUrl\n";
        return false;
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
     * Getting amazon_cba action url
     *
     * @return string
     */
    public function getPaymentAction()
    {
        $paymentAction = Mage::getStoreConfig('payment/amazon_cna/payment_action');
        if (!$paymentAction) {
            $paymentAction = Mage_AmazonPayments_Model_Api::PAYMENT_TYPE_AUTH;
        }
        return $paymentAction;
    }

    /**
     * Making API call to start transaction from shopping cart
     *
     * @return Mage_AmazonPayment_Model_Payment_Cba
     */
    public function shortcutSetCbaCheckout()
    {
        $_quote = $this->getCheckout()->getQuote();

        $this->getCheckout()->getQuote()->reserveOrderId()->save();

        $this->getApi()
            ->setPaymentType($this->getPaymentAction())
            ->setAmount($_quote->getBaseGrandTotal())
            ->setCurrencyCode($_quote->getBaseCurrencyCode())
            ->setInvNum($_quote->getReservedOrderId());
            #->callSetCbaCheckout();

        $this->catchError();

        $this->getSession()->setExpressCheckoutMethod('shortcut');

        return $this;
    }

    public function getCheckoutFormFields()
    {
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
        /*
        //we validate currency before sending to Amazon so following code is obsolete

        if (!in_array($currency_code,$this->_allowCurrencyCode)) {
            //if currency code is not allowed currency code, use USD as default
            $storeCurrency = Mage::getSingleton('directory/currency')
                ->load($_quote->getStoreCurrencyCode());
            $amount = $storeCurrency->convert($amount, 'USD');
            $currency_code='USD';
        }
        */

        #Mage_Sales_Model_Quote
        $sArr = array();
        $i = 1;
        $_merchant_id = $this->getMerchantId();
        foreach ($_quote->getAllVisibleItems() as $_item) {
            $sArr = array_merge($sArr, array(
                "item_merchant_id_{$i}"         => Mage::getStoreConfig('payment/amazon_cba/merchant_id'),
                "item_title_{$i}"               => $_item->getName(),
                "item_description_{$i}"         => $_item->getDescription(),
                "item_price_{$i}"               => $_item->getPrice(),
                "item_quantity_{$i}"            => $_item->getQty(),
                "item_sku_{$i}"                 => $_item->getSku(),
                #"item_category_{$i}"            => $_item->getProduct()->getData('category_ids'),
                "item_tax_rate_{$i}"            => $_item->getTaxAmount(),
                #"item_tax_state_region_{$i}"    => $_item->get
                #"item_promotion_type_{$i}"      => $_item->get
                "item_weight_{$i}"              => $_item->getWeight(),
            ));
        }

        /*echo '<pre> quote:'."\n";
        print_r($_quote->getData());
        echo '</pre>'."\n";*/

        /*'business'          => Mage::getStoreConfig('paypal/wps/business_account'),
        'return'            => Mage::getUrl('paypal/standard/success',array('_secure' => true)),
        'cancel_return'     => Mage::getUrl('paypal/standard/cancel',array('_secure' => false)),
        'notify_url'        => Mage::getUrl('paypal/standard/ipn'),*/

        $sArr = array_merge($sArr, array(
            'currency_code'                     => $currency_code,
            'tax_rate'                          => 1,
            'invoice'                           => $this->getCheckout()->getLastRealOrderId(),
            'address_override'                  => 1,
            'first_name'                        => $a->getFirstname(),
            'last_name'                         => $a->getLastname(),
            'address1'                          => $a->getStreet(1),
            'address2'                          => $a->getStreet(2),
            'city'                              => $a->getCity(),
            'state'                             => $a->getRegionCode(),
            'country'                           => $a->getCountry(),
            'zip'                               => $a->getPostcode(),
            #'tax_rate'                          => '0.8',
            #'tax_state_region'                  => 'LA',
            #'is_shipping_taxed'                 => 'no',
            #'tax_state_region'                  => 'LA',
            #'shipping_method_service_level_1'   => 'standard',
            #'shipping_method_region_1'          => 'us_full_50_states',
            #'shipping_method_price_type_1'      => 'weight_based',
            #'shipping_method_price_per_shipment_amount_1'      => '7.49',
            #'shipping_method_price_per_unit_rate_1'      => '5.00',

            #'cart_promotion_1'                  => '.05',
            #'cart_promotion_type_1'             => 'discount_rate',

            #'weight_unit'                       => 'lb',
        ));
        /*
        $logoUrl = Mage::getStoreConfig('paypal/wps/logo_url');
        if($logoUrl){
             $sArr = array_merge($sArr, array(
                  'cpp_header_image' => $logoUrl
             ));
        }
        */

        if($this->getConfigData('payment_action')==self::PAYMENT_TYPE_AUTH){
             $sArr = array_merge($sArr, array(
                  'paymentaction' => 'authorization'
             ));
        }

        /*
        $transaciton_type = $this->getConfigData('transaction_type');
        //O=aggregate cart amount to paypal
        //I=individual items to paypal
        if ($transaciton_type=='O') {
            $businessName = Mage::getStoreConfig('paypal/wps/business_name');
            $storeName = Mage::getStoreConfig('store/system/name');
            $amount = ($a->getBaseSubtotal()+$b->getBaseSubtotal())-($a->getBaseDiscountAmount()+$b->getBaseDiscountAmount());
            $sArr = array_merge($sArr, array(
                    'cmd'           => '_ext-enter',
                    'redirect_cmd'  => '_xclick',
                    'item_name'     => $businessName ? $businessName : $storeName,
                    'amount'        => sprintf('%.2f', $amount),
                ));
            $_shippingTax = $_quote->getShippingAddress()->getBaseTaxAmount();
            $_billingTax = $_quote->getBillingAddress()->getBaseTaxAmount();
            $tax = sprintf('%.2f', $_shippingTax + $_billingTax);
            if ($tax>0) {
                  $sArr = array_merge($sArr, array(
                        'tax' => $tax
                  ));
            }

        } else {
            $sArr = array_merge($sArr, array(
                'cmd'       => '_cart',
                'upload'       => '1',
            ));
            $items = $_quote->getAllItems();
            if ($items) {
                $i = 1;
                foreach($items as $item){
                    if ($item->getParentItem()) {
                        continue;
                    }
                    //echo "<pre>"; print_r($item->getData()); echo"</pre>";
                    $sArr = array_merge($sArr, array(
                        'item_name_'.$i      => $item->getName(),
                        'item_number_'.$i      => $item->getSku(),
                        'quantity_'.$i      => $item->getQty(),
                        'amount_'.$i      => sprintf('%.2f', ($item->getBaseCalculationPrice() - $item->getBaseDiscountAmount())),
                    ));
                    if($item->getBaseTaxAmount()>0){
                        $sArr = array_merge($sArr, array(
                        'tax_'.$i      => sprintf('%.2f',$item->getBaseTaxAmount()/$item->getQty()),
                        ));
                    }
                    $i++;
                }
           }
        }

        $totalArr = $a->getTotals();
        $shipping = sprintf('%.2f', $_quote->getShippingAddress()->getBaseShippingAmount());
        if ($shipping>0 && !$_quote->getIsVirtual()) {
          if ($transaciton_type=='O') {
              $sArr = array_merge($sArr, array(
                    'shipping' => $shipping
              ));
          } else {
              $shippingTax = $_quote->getShippingAddress()->getBaseShippingTaxAmount();
              $sArr = array_merge($sArr, array(
                    'item_name_'.$i   => $totalArr['shipping']->getTitle(),
                    'quantity_'.$i    => 1,
                    'amount_'.$i      => sprintf('%.2f',$shipping),
                    'tax_'.$i         => sprintf('%.2f',$shippingTax),
              ));
              $i++;
          }
        }
        */

        $sReq = '';
        $rArr = array();
        foreach ($sArr as $k=>$v) {
            /*
            replacing & char with and. otherwise it will break the post
            */
            $value =  str_replace("&","and",$v);
            $rArr[$k] =  $value;
            $sReq .= '&'.$k.'='.$value;
        }

        /*if ($this->getDebug() && $sReq) {
            $sReq = substr($sReq, 1);
            $debug = Mage::getModel('paypal/api_debug')
                    ->setApiEndpoint($this->getPaypalUrl())
                    ->setRequestBody($sReq)
                    ->save();
        }*/
        /*echo '<pre> rArr:'."\n";
        print_r($rArr);
        echo '</pre>'."\n";*/

        return $rArr;
    }


    public function canCapture()
    {
        return true;
    }

    /**
     * initialize payment transaction in case
     * we doing checkout through onepage checkout
     */
    public function initialize($paymentAction, $stateObject)
    {
        $_quote = $this->getCheckout()->getQuote();
        $address = $_quote->getBillingAddress();

        $this->getApi()
            ->setPaymentType($paymentAction)
            ->setAmount($address->getBaseGrandTotal())
            ->setCurrencyCode($_quote->getBaseCurrencyCode())
            ->setBillingAddress($address)
            ->setCardId($_quote->getReservedOrderId())
            ->setCustomerName($_quote->getCustomer()->getName());
            #->callSetExpressCheckout();

        #$this->throwError();

        $stateObject->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
        $stateObject->setStatus('pending_worldpay');
        $stateObject->setIsNotified(false);

        Mage::getSingleton('worldpay/session')->unsExpressCheckoutMethod();

        return $this;
    }

    /**
     * Rewrite standard logic
     *
     * @return bool
     */
    public function isInitializeNeeded()
    {
        return true;
    }

    /**
     * Processing error from amazon
     *
     * @return Mage_AmazonPayments_Model_Payment_Cba
     */
    public function catchError()
    {
        if ($this->getApi()->getError()) {
            $s = $this->getCheckout();
            $e = $this->getApi()->getError();
            switch ($e['type']) {
                case 'CURL':
                    $s->addError(Mage::helper('amazonpayments')->__('There was an error connecting to the Amazon server: %s', $e['message']));
                    break;

                case 'API':
                    $s->addError(Mage::helper('amazonpayments')->__('There was an error during communication with Amazon: %s - %s', $e['short_message'], $e['long_message']));
                    break;
            }
        }
        return $this;
    }
    /**
     * Works same as catchError method but instead of saving
     * error message in session throws exception
     *
     * @return Mage_AmazonPayments_Model_Payment_Cba
     */
    public function throwError()
    {
        if ($this->getApi()->getError()) {
            $s = $this->getCheckout();
            $e = $this->getApi()->getError();
            switch ($e['type']) {
                case 'CURL':
                    Mage::throwException(Mage::helper('amazonpayments')->__('There was an error connecting to the Amazon server: %s', $e['message']));
                    break;

                case 'API':
                    Mage::throwException(Mage::helper('amazonpayments')->__('There was an error during communication with Amazon: %s - %s', $e['short_message'], $e['long_message']));
                    break;
            }
        }
        return $this;
    }
}