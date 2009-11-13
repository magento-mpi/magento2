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
 * @package     Mage_Moneybookers
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class Mage_Moneybookers_Model_Abstract extends Mage_Payment_Model_Method_Abstract
{
    const XML_PATH_EMAIL	= 'moneybookers/settings/moneybookers_email';

    /**
    * unique internal payment method identifier
    *
    * @var string [a-z0-9_]
    **/
    protected $_code = 'moneybookers_abstract';

    protected $_formBlockType = 'moneybookers/form';
    protected $_infoBlockType = 'moneybookers/info';

    protected $_isGateway				= true;
    protected $_canAuthorize			= true;
    protected $_canCapture				= true;
    protected $_canCapturePartial		= false;
    protected $_canRefund				= false;
    protected $_canVoid					= false;
    protected $_canUseInternal			= false;
    protected $_canUseCheckout			= true;
    protected $_canUseForMultishipping	= false;

    protected $_paymentMethod			= 'abstract';
    protected $_defaultLocale			= 'en';
    protected $_supportedLocales		= array('cn', 'cz', 'en', 'es', 'de', 'fr', 'gr', 'it', 'nl', 'ro', 'ru', 'pl', 'tr');
    protected $_hidelogin				= '1';

    protected $_order;

    /**
     * Get order model
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $this->_order = $this->getInfoInstance()->getOrder();
        }
        return $this->_order;
    }

    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('moneybookers/processing/payment', array('_secure'=>true));
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }

    public function cancel(Varien_Object $payment)
    {
        $payment->setStatus(self::STATUS_DECLINED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }

    /**
     * Return url of payment method
     *
     * @return string
     */
    public function getUrl()
    {
         return 'https://www.moneybookers.com/app/payment.pl';
    }

    /**
     * Return url of payment method
     *
     * @return string
     */
    public function getLocale()
    {
        $locale = explode('_', Mage::app()->getLocale()->getLocaleCode());
        if (is_array($locale) && !empty($locale) && in_array($locale[0], $this->_supportedLocales))
            return $locale[0];
        else
            return $this->getDefaultLocale();
    }

    /**
     * prepare params array to send it to gateway page via POST
     *
     * @return array
     */
    public function getFormFields()
    {
        $order_id	= $this->getOrder()->getRealOrderId();
        $billing	= $this->getOrder()->getBillingAddress();
        if ($this->getOrder()->getBillingAddress()->getEmail()) {
            $email = $this->getOrder()->getBillingAddress()->getEmail();
        } else {
            $email = $this->getOrder()->getCustomerEmail();
        }

        $params = 	array(
                        'merchant_fields'		=> 'partner',
                        'partner'				=> 'magento',
                        'pay_to_email'			=> Mage::getStoreConfig(self::XML_PATH_EMAIL),
                        'transaction_id'		=> $order_id,
                        'return_url'			=> Mage::getUrl('moneybookers/processing/checkresponse', array('order_id' => $order_id, 'status' => 'success', '_secure' => true)),
                        'cancel_url'			=> Mage::getUrl('moneybookers/processing/checkresponse', array('order_id' => $order_id, 'status' => 'cancel', '_secure' => true)),
                        'status_url'			=> Mage::getUrl('moneybookers/processing/status', array('_secure'=>true)),
                        'language'				=> $this->getLocale(),
                        'amount'				=> round($this->getOrder()->getBaseGrandTotal(), 2),
                        'currency'				=> $this->getOrder()->getBaseCurrencyCode(),
                        'recipient_description'	=> $this->getOrder()->getStore()->getWebsite()->getName(),
                        'firstname'				=> $billing->getFirstname(),
                        'lastname'				=> $billing->getLastname(),
                        'address'				=> $billing->getStreet(-1),
                        'postal_code'			=> $billing->getPostcode(),
                        'city'					=> $billing->getCity(),
                        'country'				=> $billing->getCountryModel()->getIso3Code(),
                        'pay_from_email'		=> $email,
                        'phone_number'			=> $billing->getTelephone(),
                        'detail1_description'	=> Mage::helper('moneybookers')->__('Order ID'),
                        'detail1_text'			=> $order_id,
                        'payment_methods'		=> $this->_paymentMethod,
                        'hide_login'			=> $this->_hidelogin,
                        'new_window_redirect'	=> '1'
                    );

            // add optional day of birth
        if ($billing->getDob())
            $params['date_of_birth'] = Mage::app()->getLocale()->date($billing->getDob(), null, null, false)->toString('dmY');

        return $params;
    }
}

