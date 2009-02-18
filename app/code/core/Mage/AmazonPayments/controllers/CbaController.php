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

class Mage_AmazonPayments_CbaController extends Mage_Core_Controller_Front_Action
{
    /**
     * Test Action for Calculation Callback
     *
     */
    public function indexAction()
    {
        $html = "\n"
            .'<form method="post" action="http://kv.no-ip.org/dev/andrey.babich/magento/index.php/amazonpayments/cba/callback/"><table>'."\n"
            .'<tr><td>Signature</td><td><input type="input" size="60" name="Signature" value="92z5BOJa36KYt4oVfkp+gZhDJx0="></td></tr>'."\n"
            .'<tr><td>UUID</td><td><input type="input" size="60" name="UUID" value="a8463aad-88d7-4922-830c-a23d4a677d04"></td></tr>'."\n"
            .'<tr><td>order-calculations-request</td><td><textarea name="order-calculations-request" cols="80" rows="40">'."\n"
            .'<?xml version="1.0" encoding="UTF-8"?>
<OrderCalculationsRequest xmlns="http://payments.amazon.com/checkout/2008-11-30/">
     <CallbackReferenceId>2-90aec5b2-cd93-4cd4-af5c-86df5c469963</CallbackReferenceId>
       <OrderCalculationCallbacks>
       <CalculateTaxRates>true</CalculateTaxRates>
       <CalculatePromotions>true</CalculatePromotions>
       <CalculateShippingRates>true</CalculateShippingRates>
       <OrderCallbackEndpoint>http://kv.no-ip.org/dev/andrey.babich/magento/index.php/amazonpayments/cba/success/</OrderCallbackEndpoint>
       <ProcessOrderOnCallbackFailure>false</ProcessOrderOnCallbackFailure>
      </OrderCalculationCallbacks>
<IntegratorId>A2ZZYWSJ0WMID8</IntegratorId>
<IntegratorName>Varien</IntegratorName>
<Cart>
      <Items>
      <Item>
       <SKU>product_1</SKU>
       <MerchantId>A2ZZYWSJ0WMID8</MerchantId>
       <Title>Product 1</Title>
       <Price>
        <Amount>1.0500</Amount>
        <CurrencyCode>USD</CurrencyCode>
       </Price>
       <Quantity>2</Quantity>
       <Weight>
         <Amount>1</Amount>
          <Unit>lb</Unit>
        </Weight>
      </Item>
      <Item>
       <SKU>product_2</SKU>
       <MerchantId>A2ZZYWSJ0WMID8</MerchantId>
       <Title>Product 2</Title>
       <Price>
        <Amount>2.4000</Amount>
        <CurrencyCode>USD</CurrencyCode>
       </Price>
       <Quantity>1</Quantity>
       <Weight>
         <Amount>1</Amount>
          <Unit>lb</Unit>
        </Weight>
      </Item>
      </Items>
</Cart>
<CallbackOrders>
     <CallbackOrder>
      <Address>
       <AddressId>d89f3a35931c386956c1a402a8e09941</AddressId>
       <AddressFieldOne>Temerjazevska 66/1</AddressFieldOne>
       <AddressFieldTwo/>
       <AddressFieldThree/>
       <City>NY</City>
       <State>NY</State>
       <PostalCode>10001</PostalCode>
       <CountryCode>US</CountryCode>
      </Address>
      <CallbackOrderItems>
       <CallbackOrderItem>
        <SKU>product_1</SKU>
       </CallbackOrderItem>
       <CallbackOrderItem>
        <SKU>product_2</SKU>
       </CallbackOrderItem>
      </CallbackOrderItems>
     </CallbackOrder>
</CallbackOrders>
</OrderCalculationsRequest>'
            .'</textarea></td></tr>'."\n"
            .'<tr><td>Timestamp</td><td><input type="input" size="60" name="Timestamp" value="2009-02-05T%2012%3A58%3A24.292Z"></td></tr>'."\n"
            .'<tr><td rowspan="2"><input type="submit" name="submit" value="submit"></td></tr>'."\n"
            ."</table></form>\n"
            ."\n";
        echo $html;
        return true;
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
     * Get singleton with Checkout by Amazon order transaction information
     *
     * @return Mage_AmazonPayments_Model_Payment_CBA
     */
    public function getCba()
    {
        return Mage::getSingleton('amazonpayments/payment_cba');
    }

    /**
     * When a customer chooses Checkout by Amazon on Shopping Cart page
     *
     */
    public function shortcutAction()
    {
        if (!$this->getCba()->isAvailable()) {
            $this->_redirect('checkout/cart/');
        }
        $session = $this->getCheckout();
        $_quote = $this->getCheckout()->getQuote();
        $payment = $this->getCheckout()->getQuote()->getPayment();

        $this->getResponse()->setBody($this->getLayout()->createBlock('amazonpayments/cba_redirect')->toHtml());
        $session->unsQuoteId();
    }

    /**
     * When a customer has checkout on Amazon and return with Successful payment
     *
     */
    public function successAction()
    {
        $error_message = '';

        /*Array
        (
            [amznPmtsOrderIds] => 102-7389301-2720225
            [showAmznPmtsTYPopup] => 1
            [merchName] => Varien
            [amznPmtsYALink] => http://kv.no-ip.org/dev/andrey.babich/magento/index.php/amazonpayments/cba/return/?amznPmtsOrderIds=102-7389301-272022&
        )*/
        $_request = Mage::app()->getRequest()->getParams();
        $referenceId = Mage::app()->getRequest()->getParam('amznPmtsOrderIds');

        $this->getCba()->returnAmazon();

        $quote = $this->getCheckout()->getQuote();

        $billing = $quote->getBillingAddress();
        $shipping = $quote->getShippingAddress();

        $convertQuote = Mage::getModel('sales/convert_quote');
        /* @var $convertQuote Mage_Sales_Model_Convert_Quote */

        //$order = $convertQuote->toOrder($quote);

        $order = $convertQuote->addressToOrder($billing);
        /* @var $order Mage_Sales_Model_Order */

        // add payment information to order
        $order->setBillingAddress($convertQuote->addressToOrderAddress($billing));
        $order->setShippingAddress($convertQuote->addressToOrderAddress($shipping));
        $order->setPayment($convertQuote->paymentToOrderPayment($quote->getPayment()));
        #$order->setReferenceId($referenceId);

        // add items to order
        foreach ($quote->getAllItems() as $item) {
            $order->addItem($convertQuote->itemToOrderItem($item));
        }

        $order->place();

        $customer = $quote->getCustomer();
        if (isset($customer) && $customer) { // && $quote->getCheckoutMethod()=='register') {
            #$customer->save();
            #$customer->setDefaultBilling($customerBilling->getId());
            #$customerShippingId = isset($customerShipping) ? $customerShipping->getId() : $customerBilling->getId();
            #$customer->setDefaultShipping($customerShippingId);
            #$customer->save();

            $order->setCustomerId($customer->getId())
                ->setCustomerEmail($customer->getEmail())
                ->setCustomerPrefix($customer->getPrefix())
                ->setCustomerFirstname($customer->getFirstname())
                ->setCustomerMiddlename($customer->getMiddlename())
                ->setCustomerLastname($customer->getLastname())
                ->setCustomerSuffix($customer->getSuffix())
                ->setCustomerGroupId($customer->getGroupId())
                ->setCustomerTaxClassId($customer->getTaxClassId());

            #$billing->setCustomerId($customer->getId())->setCustomerAddressId($customerBilling->getId());
            #$shipping->setCustomerId($customer->getId())->setCustomerAddressId($customerShippingId);
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

        if ($this->getCba()->getDebug()) {
            $debug = Mage::getModel('amazonpayments/api_debug')
                ->setRequestBody(print_r($_request, 1))
                ->setResponseBody(time().' success')
                ->save();
        }

        $this->_redirect('checkout/onepage/success');
    }

    /**
     * When Amazon return callback request for calculation shipping, taxes and etc.
     *
     */
    public function callbackAction()
    {
        $response = '';
        $session = Mage::getSingleton('checkout/session');
        if (!$session->getQuote()->hasItems()) {
            $this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
        }

        $_request = Mage::app()->getRequest()->getParams();

        try {

            // Check incoming signature
            // incoming and generated (by the guide) not equal
            /*$_uuid = Mage::app()->getRequest()->getParam('UUID');
            $_signature = Mage::app()->getRequest()->getParam('Signature');
            $_timestamp = Mage::app()->getRequest()->getParam('Timestamp');

            $_uuid = urldecode($_uuid);
            $_timestamp = urldecode($_timestamp);
            echo "UUID: {$_uuid}<br />\n";
            echo "timestamp: ". $_timestamp ."<br /><br />\n\n";
            $_sign = $this->getCba()->getApi()->calculateSignature($_uuid.$_timestamp, Mage::getStoreConfig('payment/amazonpayments_cba/secretkey_id'));
            echo "sign: {$_sign}<br />\n";
            echo "sign: {$_signature}<br />\n";*/
            // -- end -- Check incoming signature --

            if ($_request) {
                $response = $this->getCba()->handleCallback($_request);
            } else {
                $e = new Exception('Inavlid Shipping Address');
            }
        }
        catch (Exception $e) {
            // Return Xml with Error
            $response = $this->getCba()->callbackXmlError($e);
        }
        echo $response;
        return true;
    }

    /**
     * When a customer has checkout on Amazon and return with Cancel
     *
     */
    public function cancelAction()
    {
        #die('cancel');
        $this->_redirect('checkout/cart/');
    }

}