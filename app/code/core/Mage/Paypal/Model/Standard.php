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
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * PayPal Standard Checkout Module
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    //changing the payment to different from cc payment type and paypal payment type
    const PAYMENT_TYPE_AUTH = 'AUTHORIZATION';
    const PAYMENT_TYPE_SALE = 'SALE';

    const DATA_CHARSET = 'utf-8';

    protected $_code  = 'paypal_standard';
    protected $_formBlockType = 'paypal/standard_form';
    protected $_allowCurrencyCode = array('AUD', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MXN', 'NOK', 'NZD', 'PLN', 'GBP', 'SGD', 'SEK', 'CHF', 'USD');

    /**
     * Check method for processing with base currency
     *
     * @param string $currencyCode
     * @return boolean
     */
    public function canUseForCurrency($currencyCode)
    {
        if (!in_array($currencyCode, $this->_allowCurrencyCode)) {
            return false;
        }
        return true;
    }

    /**
     * Fields that should be replaced in debug with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = array('business');

     /**
     * Get paypal session namespace
     *
     * @return Mage_Paypal_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('paypal/session');
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
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    /**
     * Using internal pages for input payment data
     *
     * @return bool
     */
    public function canUseInternal()
    {
        return false;
    }

    /**
     * Using for multiple shipping address
     *
     * @return bool
     */
    public function canUseForMultishipping()
    {
        return false;
    }

    /**
     * Create main block for standard form
     *
     */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('paypal/standard_form', $name)
            ->setMethod('paypal_standard')
            ->setPayment($this->getPayment())
            ->setTemplate('paypal/standard/form.phtml');

        return $block;
    }

    /*validate the currency code is avaialable to use for paypal or not*/
    public function validate()
    {
        parent::validate();
        $currency_code = $this->getQuote()->getBaseCurrencyCode();
        if (!in_array($currency_code,$this->_allowCurrencyCode)) {
            Mage::throwException(Mage::helper('paypal')->__('Selected currency code ('.$currency_code.') is not compatible with PayPal'));
        }
        return $this;
    }

    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
       return $this;
    }

    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Payment $payment)
    {

    }

    /**
     * Return Order place redirect url
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('paypal/standard/redirect', array('_secure' => true));
    }

    /**
     * Return form field array
     *
     * @return array
     */
    public function getStandardCheckoutFormFields()
    {
        if ($this->getQuote()->getIsVirtual()) {
            $a = $this->getQuote()->getBillingAddress();
            $b = $this->getQuote()->getShippingAddress();
        } else {
            $a = $this->getQuote()->getShippingAddress();
            $b = $this->getQuote()->getBillingAddress();
        }
        //getQuoteCurrencyCode
        $currency_code = $this->getQuote()->getBaseCurrencyCode();
        /*
        //we validate currency before sending paypal so following code is obsolete

        if (!in_array($currency_code,$this->_allowCurrencyCode)) {
            //if currency code is not allowed currency code, use USD as default
            $storeCurrency = Mage::getSingleton('directory/currency')
                ->load($this->getQuote()->getStoreCurrencyCode());
            $amount = $storeCurrency->convert($amount, 'USD');
            $currency_code='USD';
        }
        */

        $sArr = array(
            'charset'           => self::DATA_CHARSET,
            'business'          => Mage::getStoreConfig('paypal/wps/business_account'),
            'return'            => Mage::getUrl('paypal/standard/success',array('_secure' => true)),
            'cancel_return'     => Mage::getUrl('paypal/standard/cancel',array('_secure' => false)),
            'notify_url'        => Mage::getUrl('paypal/standard/ipn'),
            'invoice'           => $this->getCheckout()->getLastRealOrderId(),
            'currency_code'     => $currency_code,
            'address_override'  => 1,
            'first_name'        => $a->getFirstname(),
            'last_name'         => $a->getLastname(),
            'address1'          => $a->getStreet(1),
            'address2'          => $a->getStreet(2),
            'city'              => $a->getCity(),
            'state'             => $a->getRegionCode(),
            'country'           => $a->getCountry(),
            'zip'               => $a->getPostcode(),
            'bn'                => 'Varien_Cart_WPS_US'
        );

        $logoUrl = Mage::getStoreConfig('paypal/style/logo_url');
        if($logoUrl){
             $sArr = array_merge($sArr, array(
                  'cpp_header_image' => $logoUrl
             ));
        }

        if($this->getConfigData('payment_action')==self::PAYMENT_TYPE_AUTH){
             $sArr = array_merge($sArr, array(
                  'paymentaction' => 'authorization'
             ));
        }

        $transaciton_type = $this->getConfigData('transaction_type');
        /*
        O=aggregate cart amount to paypal
        I=individual items to paypal
        */
        if ($transaciton_type=='O') {
            $businessName = Mage::getStoreConfig('paypal/wps/business_name');
            $storeName = Mage::getStoreConfig('system/store/name');
            $amount = $a->getBaseSubtotal()+$b->getBaseSubtotal()+$a->getBaseDiscountAmount()+$b->getBaseDiscountAmount();
            $sArr = array_merge($sArr, array(
                    'cmd'           => '_ext-enter',
                    'redirect_cmd'  => '_xclick',
                    'item_name'     => $businessName ? $businessName : $storeName,
                    'amount'        => sprintf('%.2f', $amount),
                ));
            $_shippingTax = $this->getQuote()->getShippingAddress()->getBaseTaxAmount();
            $_billingTax = $this->getQuote()->getBillingAddress()->getBaseTaxAmount();
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
            $items = $this->getQuote()->getAllItems();
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
                        'amount_'.$i      => sprintf('%.2f', ($item->getBaseCalculationPrice() + $item->getBaseDiscountAmount())),
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
        $shipping = sprintf('%.2f', $this->getQuote()->getShippingAddress()->getBaseShippingAmount());
        if ($shipping>0 && !$this->getQuote()->getIsVirtual()) {
          if ($transaciton_type=='O') {
              $sArr = array_merge($sArr, array(
                    'shipping' => $shipping
              ));
          } else {
              $shippingTax = $this->getQuote()->getShippingAddress()->getBaseShippingTaxAmount();
              $sArr = array_merge($sArr, array(
                    'item_name_'.$i   => $totalArr['shipping']->getTitle(),
                    'quantity_'.$i    => 1,
                    'amount_'.$i      => sprintf('%.2f',$shipping),
                    'tax_'.$i         => sprintf('%.2f',$shippingTax),
              ));
              $i++;
          }
        }

        $sReq = '';
        $sReqDebug = '';
        $rArr = array();


        foreach ($sArr as $k=>$v) {
            /*
            replacing & char with and. otherwise it will break the post
            */
            $value =  str_replace("&","and",$v);
            $rArr[$k] =  $value;
            $sReq .= '&'.$k.'='.$value;
            $sReqDebug .= '&'.$k.'=';
            if (in_array($k, $this->_debugReplacePrivateDataKeys)) {
                $sReqDebug .= '***';
            } else  {
                $sReqDebug .= $value;
            }
        }

        if ($this->getDebug() && $sReq) {
            $sReq = substr($sReq, 1);
            $debug = Mage::getModel('paypal/api_debug')
                    ->setApiEndpoint($this->getPaypalUrl())
                    ->setRequestBody($sReq)
                    ->save();
        }
        return $rArr;
    }

    /**
     * Return request url, all information will be sended to this url
     * @return string
     */
    public function getPaypalUrl()
    {
         if (Mage::getStoreConfig('paypal/wps/sandbox_flag')==1) {
             $url='https://www.sandbox.paypal.com/cgi-bin/webscr';
         } else {
             $url='https://www.paypal.com/cgi-bin/webscr';
         }
         return $url;
    }

    /**
     * Get debug flag value
     *
     * @return bool
     */
    public function getDebug()
    {
        return Mage::getStoreConfig('paypal/wps/debug_flag');
    }


    /**
     * Process IPN request, store data in comments
     */
    public function ipnPostSubmit()
    {
        $ipn = Mage::getModel('paypal/api_ipn');
        $ipn->setIpnFormData($this->getIpnFormData())->processIpnRequest();
    }

    /**
     * Get initialized flag status
     * @return true
     */
    public function isInitializeNeeded()
    {
        return true;
    }

    /**
     * Instantiate state and set it to state onject
     * //@param
     * //@param
     */
    public function initialize($paymentAction, $stateObject)
    {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus('pending_paypal');
        $stateObject->setIsNotified(false);
    }
}
