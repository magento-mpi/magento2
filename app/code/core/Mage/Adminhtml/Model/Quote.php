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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Adminhtml_Model_Quote extends Mage_Core_Model_Session_Abstract
{

    /**
     * Admin order creation quote
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote = null;

    /**
     * Enter description here...
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer = null;

    /**
     * Enter description here...
     *
     * @var Mage_Directory_Model_Currency
     */
    protected $_currency = null;

    public function __construct()
    {
        $this->init('quote');
    }

    /**
     * Enter description here...
     *
     * @return Mage_Adminhtml_Model_Quote
     */
    public function unsetAll()
    {
        parent::unsetAll();
        $this->_quote = null;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if (empty($this->_quote)) {
            $quote = Mage::getModel('sales/quote');
            /* @var $quote Mage_Sales_Model_Quote */
            if ($this->getQuoteId()) {
                $quote->load($this->getQuoteId());
                if (!$quote->getId()) {
                    $quote->setQuoteId(null);
                }
            }
            if (!$this->getQuoteId()) {
                $quote->initNewQuote()
                    ->setStoreId($this->getStoreId())
                    ->setCustomerId($this->getCustomerId())
                ;

                $address = Mage::getModel('sales/quote_address');
                /* @var $address Mage_Sales_Model_Quote_Address */
                if ($this->getIsOldCustomer() && $this->getCustomer()->getDefaultBillingAddress()) {
                    $address->importCustomerAddress($this->getCustomer()->getDefaultBillingAddress());
                }
                $quote->setBillingAddress($address);

                $address = Mage::getModel('sales/quote_address');
                /* @var $address Mage_Sales_Model_Quote_Address */
                if ($this->getIsOldCustomer() && $this->getCustomer()->getDefaultShippingAddress()) {
                    $address->importCustomerAddress($this->getCustomer()->getDefaultShippingAddress());
                }
                $quote->setShippingAddress($address);

                $quote->save();

                $this->setQuoteId($quote->getId());
            }
            $this->_quote = $quote;
        }
        return $this->_quote;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Adminhtml_Model_Quote
     */
    public function reset()
    {
        $this->unsetAll();
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Adminhtml_Model_Quote
     */
    public function setCustomerId($customerId)
    {
        if ($oldCustomerId = $this->getCustomerId()) {
            if ($oldCustomerId != $customerId) {
                $this->reset();
            }
        }
        $this->setData('customer_id', $customerId);
        if (intval($customerId)) {
            $this->setCustomer(Mage::getModel('customer/customer')->load($customerId));
        }
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Adminhtml_Model_Quote
     */
    public function setCustomer($customer)
    {
        $this->_customer = $customer;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (is_null($this->_customer)) {
            $customer = Mage::getModel('customer/customer');
            if (($customerId = $this->getCustomerId()) && intval($customerId)) {
                $customer->load($customerId);
            }
            $this->setCustomer($customer);
        }
        return $this->_customer;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getCustomerName()
    {
        if ($this->getIsOldCustomer()) {
            return $this->getCustomer()->getName();
        } elseif ('new' === $this->getCustomerId()) {
            return Mage::helper('adminhtml')->__('new customer');
        }
        return '';
    }

    /**
     * Enter description here...
     *
     * @param int $storeId
     * @return Mage_Adminhtml_Model_Quote
     */
    public function setStoreId($storeId)
    {
        $this->setData('store_id', $storeId);

        if (! in_array($storeId, $this->getCustomer()->getSharedStoreIds())) {
            $customer = clone $this->getCustomer();
            $customer->setStoreId($storeId);
            $customer->save();
        }
        return $this;
    }

    /**
     * Get customer's front-end quote
     *
     * @param bool $create create quote if still not exists
     * @return Mage_Sales_Model_Quote|false
     */
    public function getCustomerQuote($create = true)
    {
        if ($this->getIsOldCustomer()) {
            $quote = Mage::getModel('sales/quote');
            /* @var $quote Mage_Sales_Model_Quote */
            $loadedQuote = $quote->getResourceCollection()->loadByCustomerId($this->getCustomerId());
            if ($loadedQuote) {
                return $loadedQuote;
            }
            $quote->initNewQuote()
                ->setStoreId($this->getStoreId())
                ->setCustomerId($this->getCustomerId())
                ->save();
            return $quote;
        }
        return false;
    }

    /**
     * Enter description here...
     *
     * @return boolean
     */
    public function getIsOldCustomer()
    {
        if (intval($this->getCustomerId())) {
            return true;
        }
        return false;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Directory_Model_Currency
     */
    public function getCurrency()
    {
        if (is_null($this->_currency) && $this->getStoreId()) {
            $this->setCurrency(Mage::getModel('directory/currency')->load(
                $this->getQuote()->getStore()->getConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_DEFAULT))
            );
        }
        return $this->_currency;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Directory_Model_Currency $currency
     * @return Mage_Adminhtml_Model_Quote
     */
    public function setCurrency($currency)
    {
        $this->_currency = $currency;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->getCurrency()->format($price);
    }

    /************************************************************/

//    public function getBillingAddress()
//    {
//        if ($addressId = $this->getBillingAddressId()) {
//            $address = $this->getCustomer()->getLoadedAddressCollection()->getItemById($addressId);
//        }
//        if (! $address instanceof Varien_Object) {
//            $address = $this->getQuote()->getBillingAddress();
//        }
//        if (! $address instanceof Varien_Object) {
//            $address = new Varien_Object(array());
//        }
//        return $address;
//    }
//
//    public function getShippingAddress()
//    {
//        if ($addressId = ($this->getSameAsBilling() ? $this->getBillingAddressId() : $this->getShippingAddressId())) {
//            return $this->getCustomer()->getLoadedAddressCollection()->getItemById($addressId);
//        }
//        return $this->getQuote()->getShippingAddress();
//    }

    public function saveCheckoutMethod($method)
    {
        $this->getQuote()->setCheckoutMethod($method)->save();
        $this->getCheckout()->setStepData('billing', 'allow', true);
        return $this;
    }

    public function getAddress($addressId)
    {
        $address = Mage::getModel('customer/address')->load((int)$addressId);
        $address->explodeStreetAddress();
        if ($address->getRegionId()) {
            $address->setRegion($address->getRegionId());
        }
        return $address;
    }

    public function saveBilling($data, $customerAddressId)
    {
        if (empty($data['use_for_shipping'])) {
            $data['use_for_shipping'] = 0;
        }
        else {
            $data['use_for_shipping'] = 1;
        }

        $address = $this->getQuote()->getBillingAddress();

        if (!empty($customerAddressId)) {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId()) {
                $address->importCustomerAddress($customerAddress);
            }
        } else {
            $address->addData($data);
        }
//        if (!$this->getQuote()->getCustomerId() && 'register' == $this->getQuote()->getCheckoutMethod()) {
//            $email = $address->getEmail();
//            $customer = Mage::getModel('customer/customer')->loadByEmail($email);
//            if ($customer->getId()) {
//                $res = array(
//                    'error' => 1,
//                    'message' => Mage::helper('adminhtml')->__('There is already a customer registered using this email')
//                );
//                return $res;
//            }
//        }

        $address->implodeStreetAddress();

        if (!empty($data['use_for_shipping'])) {
            $billing = clone $address;
            $billing->unsEntityId()->unsAddressType();
            $shipping = $this->getQuote()->getShippingAddress();
            $shipping->addData($billing->getData())->setSameAsBilling(1);
            $this->getQuote()->save();
            $shipping->collectShippingRates();
//            $this->getCheckout()->setStepData('shipping', 'complete', true);
        } else {
            $shipping = $this->getQuote()->getShippingAddress();
            $shipping->setSameAsBilling(0);
        }
        if ($address->getCustomerPassword()) {
            $customer = Mage::getModel('customer/customer');
            $this->getQuote()->setPasswordHash($customer->hashPassword($address->getCustomerPassword()));
        }
        $this->getQuote()->collectTotals()->save();

        return $this;
    }

    public function saveShipping($data, $customerAddressId)
    {
        $address = $this->getQuote()->getShippingAddress();

        if (!empty($customerAddressId)) {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId()) {
                $address->importCustomerAddress($customerAddress);
            }
        } else {
            $address->addData($data);
        }
        $address->implodeStreetAddress();
        $address->collectShippingRates();
        $this->getQuote()->save();

        return $this;
    }

    public function saveShippingMethod($shippingMethod)
    {
        $this->getQuote()->getShippingAddress()->setShippingMethod($shippingMethod)->collectTotals()->save();

        return $this;
    }

    public function savePayment($data)
    {
        $payment = $this->getQuote()->getPayment();
        $payment->importPostData($data);
        $this->getQuote()->save();

        return $this;
    }

    public function saveOrder()
    {
        $res = array('error'=>1);

        try {
            $billing = $this->getQuote()->getBillingAddress();
            $shipping = $this->getQuote()->getShippingAddress();

            switch ($this->getQuote()->getCheckoutMethod()) {
            case 'guest':
                $email  = $billing->getEmail();
                $name   = $billing->getFirstname().' '.$billing->getLastname();
                break;

            case 'register':
                $customer = $this->_createCustomer();
                $customer->sendNewAccountEmail();
                #$this->_emailCustomerRegistration();
                $email  = $customer->getEmail();
                $name   = $customer->getName();
                break;

            default:
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                $email  = $customer->getEmail();
                $name   = $customer->getName();
            }

            $order = Mage::getModel('sales/order')->createFromQuoteAddress($shipping);

            $order->validate();

            if ($order->getErrors()) {
                //TODO: handle errors (exception?)
            }

            $order->save();
            $this->getQuote()->setIsActive(false);
            $this->getQuote()->save();

            $orderId = $order->getIncrementId();
            $this->getCheckout()->setLastOrderId($order->getId());

            $order->sendNewOrderEmail();
            #$this->_emailOrderConfirmation($email, $name, $order);

            $res['success'] = true;
            $res['error']   = false;
            //$res['error']   = true;
        }
        catch (Exception $e){
            // TODO: create response for open checkout card with error
            echo $e;
        }

        return $res;
    }

    protected function _emailCustomerRegistration()
    {
        $customer = $this->_createCustomer();
        $mailer = Mage::getModel('customer/email')
            ->setTemplate('email/welcome.phtml')
            ->setType('html')
            ->setCustomer($customer)
            ->send();
    }

    protected function _emailOrderConfirmation($email, $name, $order)
    {
        $mailer = Mage::getModel('core/email')
            ->setTemplate('email/order.phtml')
            ->setType('html')
            ->setTemplateVar('order', $order)
            ->setTemplateVar('quote', $this->getQuote())
            ->setTemplateVar('name', $name)
            ->setToName($name)
            ->setToEmail($email)
            ->send();
    }

    protected function _createCustomer()
    {
        $customer = Mage::getModel('customer/customer');

        $billingEntity = $this->getQuote()->getBillingAddress();
        $billing = Mage::getModel('customer/address');
        $billing->addData($billingEntity->getData());
        $customer->addAddress($billing);

        $shippingEntity = $this->getQuote()-getShippingAddress();
        if (!$shippingEntity->getSameAsBilling()) {
            $shipping = Mage::getModel('customer/address');
            $shipping->addData($shippingEntity->getData());
            $customer->addAddress($shipping);
        } else {
            $shipping = $billing;
        }
        //TODO: check that right primary types are assigned

        $customer->setFirstname($billing->getFirstname());
        $customer->setLastname($billing->getLastname());
        $customer->setEmail($billing->getEmail());
        $customer->setPasswordHash($this->getQuote()->getPasswordHash());

        $customer->save();

        $this->getQuote()->setCustomerId($customer->getId());
        $billingEntity->setCustomerId($customer->getId())->setCustomerAddressId($billing->getId());
        $shippingEntity->setCustomerId($customer->getId())->setCustomerAddressId($shipping->getId());

        Mage::getSingleton('customer/session')->loginById($customer->getId());

        return $customer;
    }

    public function getLastOrderId()
    {
        $order = Mage::getModel('sales/order');
        $order->load($this->getCheckout()->getLastOrderId());
        if (!$order->getIncrementId()) {
            $this->_redirect('checkout/cart');
            return;
        }
        $orderId = $order->getIncrementId();
        return $orderId;
    }

    /************************************************************/

    /**
     * Enter description here...
     *
     * @param int $addressId
     * @return Mage_Adminhtml_Model_Quote
     */
    public function setShippingAddressId($addressId)
    {
        $this->setData('shipping_address_id', $addressId);
        $address = Mage::getModel('sales/quote_address');
        /* @var $address Mage_Sales_Model_Quote_Address */
        if ($addressId) {
            $address->importCustomerAddress($this->getAddress($addressId));
        }
        $this->getQuote()->setShippingAddress($address)->save();
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param int $addressId
     * @return Mage_Adminhtml_Model_Quote
     */
    public function setBillingAddressId($addressId)
    {
        $this->setData('billing_address_id', $addressId);
        $address = Mage::getModel('sales/quote_address');
        /* @var $address Mage_Sales_Model_Quote_Address */
        if ($addressId) {
            $address->importCustomerAddress($this->getAddress($addressId));
        }
        $this->getQuote()->setBillingAddress($address)->save();
        return $this;
    }

    public function setSameAsBilling($same=true)
    {
        $this->setData('same_as_billing', $same);
        $this->setShippingAddressId($this->getBillingAddressId());
        $address = clone $this->getQuote()->getBillingAddress();
        $this->getQuote()->setShippingAddress($address)->save();
        return $this;
    }

}
