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
            'validUntil' => gmdate('Y') . '-' . gmdate('m') . '-' . gmdate('d') . 'T'
            . gmdate('H'+1) . ':' . gmdate('i') . ':' . gmdate('s') . '.000Z'
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

        $fields = array_merge($fields, array(
            'language' => 'nl',
            'currency' => $currency_code,
            'description' => Mage::getStoreConfig('ideal/basic/description'),
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

        if ($this->getDebug() && $requestString) {
            $requestString = substr($requestString, 1);
            $debug = Mage::getModel('ideal/api_debug')
                    ->setApiEndpoint($this->getApiUrl())
                    ->setRequestBody($requestString)
                    ->save();
        }

        return $returnArray;
    }

    /**
     * Return iDEAL Basic Api Url
     *
     * @return stringphp5
     */
    public function getApiUrl()
    {
         if (Mage::getStoreConfig('ideal/basic/test') == 1) {
             $url = "https://idealtest.secure-ing.com/ideal/mpiPayInitIng.do";
         } else {
             $url = "https://ideal.secure-ing.com/ideal/mpiPayInitIng.do";
         }

         return $url;
    }

    public function getDebug()
    {
        return false;
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
