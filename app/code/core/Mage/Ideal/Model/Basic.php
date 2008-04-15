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
 * @package    Mage_Ideal
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * iDEAL Basic Checkout Model
 *
 * @category    Mage_Ideal
 * @package     Mage_Ideal
 * @author      Dmytro Vasylenko <dimav@varien.com>
 */

class Mage_Ideal_Model_Basic extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'ideal_basic';
    protected $_formBlockType = 'ideal/basic_form';
    protected $_allowCurrencyCode = array('EUR');

    protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

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
     * Get debug flag
     *
     * @return boolean
     */
    public function getDebug()
    {
        return Mage::getStoreConfig('payment/ideal_basic/debug_flag');
    }

    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('ideal/basic_form', $name)
            ->setMethod('ideal_basic')
            ->setPayment($this->getPayment())
            ->setTemplate('ideal/basic/form.phtml');

        return $block;
    }

    /**
     * validate the currency code is avaialable to use for iDEAL Basic or not
     *
     * @return bool
     */
    public function validate()
    {
        parent::validate();
        $currency_code = $this->getQuote()->getBaseCurrencyCode();
        if (!in_array($currency_code,$this->_allowCurrencyCode)) {
            Mage::throwException(Mage::helper('ideal')->__('Selected currency code ('.$currency_code.') is not compatabile with iDEAL'));
        }
        return $this;
    }

    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('ideal/basic/redirect', array('_secure' => true));
    }

    /**
     * Return iDEAL Basic Api Url
     *
     * @return string Payment API URL
     */
    public function getApiUrl()
    {
         if (Mage::getStoreConfig('ideal/basic/test_flag') == 1) {
             $url = "https://idealtest.secure-ing.com/ideal/mpiPayInitIng.do";
         } else {
             $url = "https://ideal.secure-ing.com/ideal/mpiPayInitIng.do";
         }
         return $url;
    }

    /**
     * Generates array of fields for redirect form
     *
     * @return array
     */
    public function getBasicCheckoutFormFields()
    {
        $quote = $this->getQuote();
        $quote->reserveOrderId();

        $shippingAddress = $quote->getShippingAddress();
        $currency_code = $quote->getBaseCurrencyCode();

        $fields = array(
            'merchantID' => Mage::getStoreConfig('ideal/basic/merchant_id'),
            'subID' => '0',
            'amount' => ($shippingAddress->getBaseSubtotal()-$shippingAddress->getBaseDiscountAmount())*100,
            'purchaseID' => $quote->getReservedOrderId(),
            'paymentType' => 'ideal',
            'validUntil' => gmdate('Y-m-d\TH:i:s.000\Z', time() + 60 * 60) // plus 1 hour gmmktime () ???
        );

        $i = 1;
        foreach ($quote->getItemsCollection() as $item) {
            $fields = array_merge($fields, array(
                "itemNumber".$i => $item->getSku(),
                "itemDescription".$i => $item->getName(),
                "itemQuantity".$i => $item->getQty(),
                "itemPrice".$i => $item->getPrice()*100
            ));
            $i++;
        }

        $fields = $this->appendHash($fields);

        $description = Mage::getStoreConfig('ideal/basic/description');
        if ($description == '') {
            $description = Mage::app()->getStore()->getName() . ' ' . 'payment';
        }

        $fields = array_merge($fields, array(
            'language' => 'nl',
            'currency' => $currency_code,
            'description' => $description,
            'urlCancel' => Mage::getUrl('ideal/basic/cancel', array('_secure' => true)),
            'urlSuccess' => Mage::getUrl('ideal/basic/success', array('_secure' => true)),
            'urlError' => Mage::getUrl('ideal/basic/error', array('_secure' => true))
        ));

        $requestString = '';
        $returnArray = array();

        foreach ($fields as $k=>$v) {
            $returnArray[$k] =  $v;
            $requestString .= '&'.$k.'='.$v;
        }

        if ($this->getDebug()) {
            Mage::getModel('ideal/api_debug')
                ->setRequestBody($this->getApiUrl() . "\n" . $requestString . "\n" . print_r($returnArray,1))
                ->save();
        }

        return $returnArray;
    }


    /**
     * Calculates and appends hash to form fields
     *
     * @param array $returnArray
     * @return array
     */
    public function appendHash($returnArray)
    {
        $merchantKey = Mage::getStoreConfig('ideal/basic/merchant_key');
        $hashString = $merchantKey.implode('', $returnArray);
        $hashString = str_replace(
            array(" ", "\t", "\n", "&amp;", "&lt;", "&gt;", "&quote;"),
            array("", "", "", "&", "<", ">", "\""),
            $hashString);
        $hash = sha1($hashString);
        return array_merge($returnArray, array('hash' => $hash));
    }

}