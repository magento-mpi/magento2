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
 * Chronopay Standard Model
 *
 * @category   Mage
 * @package    Mage_Chronopay
 * @name       Mage_Chronopay_Model_Standard
 * @author     Dmitriy Volik <dmitriy.volik@varien.com>
 */
class Mage_Chronopay_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'chronopay_standard';
    protected $_formBlockType = 'chronopay/standard_form';

    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    protected $_order = null;


    /**
     * Get Config model
     *
     * @return object Mage_Chronopay_Model_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('chronopay/config');
    }

    /**
     * Get checkout session namespace
     *
     * @return object Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current quote
     *
     * @return object Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    /**
     * Get created order
     *
     * @return object Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($this->_order == null) {
            $this->_order = Mage::getModel('sales/order');
            $this->_order->loadByIncrementId($this->getCheckout()->getLastRealOrderId());
        }
        return $this->_order;
    }

    /**
     *  Returns Target URL
     *
     *  @return	  string Target URL
     */
    public function getChronopayUrl ()
    {
        return 'https://secure.chronopay.com/index_shop.cgi';
    }

    /**
     *  Return URL for Chronopay success response
     *
     *  @return	  string URL
     */
    protected function getSuccessURL ()
    {
        return Mage::getUrl('chronopay/standard/success');
    }

    /**
     *  Return URL for Chronopay notification
     *
     *  @return	  string Notification URL
     */
    protected function getNotificationURL ()
    {
        return 'http://kv.no-ip.org/dev/dmitriy.volik/magento/chronopay/standard/notify';
//        return Mage::getUrl('chronopay/standard/notify');
    }

    /**
     *  Return URL for Chronopay failure response
     *
     *  @return	  string URL
     */
    protected function getFailureURL ()
    {
        return 'http://kv.no-ip.org/dev/dmitriy.volik/magento/chronopay/standard/failure';
//        return Mage::getUrl('chronopay/standard/failure');
    }

    /**
     *  Form block description
     *
     *  @return	 object
     */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('chronopay/form_standard', $name);
        $block->setMethod($this->_code);
        $block->setPayment($this->getPayment());
        return $block;
    }

    /**
     *  Return Order Place Redirect URL
     *
     *  @return	  string Order Redirect URL
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('chronopay/standard/redirect');
    }

    /**
     *  Return Standard Checkout Form Fields for request to Chronopay
     *
     *  @return	  array Array of hidden form fields
     */
    public function getStandardCheckoutFormFields ()
    {
        $billingAddress = $this->getOrder()->getShippingAddress();
        $streets = $billingAddress->getStreet();
        $street = isset($streets[0]) && $streets[0] != ''
                  ? $streets[0]
                  : (isset($streets[1]) && $streets[1] != '' ? $streets[1] : '');


        $fields = array(
                        'product_id'       => $this->getConfig()->getProductId(),
                        'product_name'     => $this->getConfig()->getDescription(),
                        'product_price'    => $this->getOrder()->getBaseGrandTotal(),
                        'language'         => 'EN',
                        'f_name'           => $this->getOrder()->getCustomerFirstname(),
                        's_name'           => $this->getOrder()->getCustomerLastname(),
                        'street'           => $street,
                        'city'             => $billingAddress->getCity(),
                        'state'            => $billingAddress->getRegion(),
                        'zip'              => $billingAddress->getPostcode(),
                        'country'          => $billingAddress->getCountry(),
                        'phone'            => $billingAddress->getTelephone(),
                        'email'            => $this->getOrder()->getCustomerEmail(),
                        'cb_url'           => $this->getNotificationURL(),
                        'cb_type'          => 'P', // POST method used (G - GET method)
                        'decline_url'      => $this->getFailureURL(),
                        'cs1'              => $this->getOrder()->getRealOrderId()
                        );
        if ($this->getConfig()->getDebug()) {
             Mage::getModel('chronopay/api_debug')
                ->setRequestBody($this->getChronopayUrl()."\n".print_r($fields,1))
                ->save();
       }

        return $fields;
    }
}