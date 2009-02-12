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

/**
 * AmazonPayments CBA API wrappers model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_AmazonPayments_Model_Api_Cba extends Mage_AmazonPayments_Model_Api_Abstract
{
    protected static $HMAC_SHA1_ALGORITHM = "sha1";
    protected $_paymentCode = 'amazonpayments_cba';

    protected $_carriers;
    protected $_address;

    /**
     * Return Merchant Id from config
     *
     * @return string
     */
    public function getMerchantId()
    {
        return Mage::getStoreConfig('payment/amazonpayments_cba/merchant_id');
    }

    /**
     * Return Intgrator Id from config
     *
     * @return string
     */
    public function getIntegratorId()
    {
        return Mage::getStoreConfig('payment/amazonpayments_cba/integrator_id');
    }

    /**
     * Return action url for CBA Cart form to Amazon
     *
     * @return unknown
     */
    public function getAmazonRedirectUrl()
    {
        #$_url = $this->getCbaPaymentUrl();
        $_url = $this->getPayServiceUrl();
        $_merchantId = Mage::getStoreConfig('payment/amazonpayments_cba/merchant_id');
        return $_url.$_merchantId;
    }

    /**
     * Return CBA Payment url in depends on Sand-box flag
     *
     * @return string
     */
    /*public function getCbaPaymentUrl()
    {
        echo "url: ".$this->getPayServiceUrl();
        #return $this->getPayServiceUrl();

        /*if ($this->getSandboxFlag()) {
            $_url = $this->getConfigData('sandbox_pay_service_url');
        } else {
            $_url = $this->getConfigData('pay_service_url');
        }* /
        #return $_url;
    }*/
    /**
     * Computes RFC 2104-compliant HMAC signature.
     *
     * @param data Array
     *            The data to be signed.
     * @param key String
     *            The signing key, a.k.a. the AWS secret key.
     * @return The base64-encoded RFC 2104-compliant HMAC signature.
     */
    public function calculateSignature($data, $secretKey)
    {
        $stringData = '';
        if (is_array($data)) {
            ksort($data);
            foreach ($data as $key => $value) {
                $stringData .= $key.'='.rawurlencode($value).'&';
            }
        } elseif (is_string($data)) {
            $stringData = $data;
        } else {
            $stringData = $data;
        }

        // compute the hmac on input data bytes, make sure to set returning raw hmac to be true
        $rawHmac = hash_hmac(self::$HMAC_SHA1_ALGORITHM, $stringData, $secretKey, true);

        // base64-encode the raw hmac
        return base64_encode($rawHmac);
    }

    /**
     *
     */
    public function getAmazonCbaOrderDetails($amazonOrderId)
    {
        $_merchantId = Mage::getStoreConfig('payment/amazonpayments_cba/merchant_id');
        $merchant = array(
            'merchantIdentifier' => $_merchantId,
            'merchantName' => Mage::getStoreConfig('payment/amazonpayments_cba/merchant_name'),
        );
        $loginOptions = array(
            'login' => 'yoav@varien.com',
            'password' => 'varien1975',
        );

        $_soap = $this->getSoapApi(array_merge($merchant, $loginOptions));

        /*$_options = array(
                'merchant'           => $_merchantId,
                'documentIdentifier' => $amazonOrderId,
            );*/
        /*$_options = array(
                'Merchant'           => stdClass::__constructor(array(
                        'merchantIdentifier' => $_merchantId,
                        'merchantName' => 'Varien')),
                'documentIdentifier' => $amazonOrderId,
            );*/
$doc = '<?xml version="1.0" encoding="UTF-8"?>
<Order xmlns="http://payments.amazon.com/checkout/2008-08-29/">
  <Cart>
    <Items>
      <Item>
        <SKU>ABC123</SKU>
        <MerchantId>'.$_merchantId.'</MerchantId>
        <Title>Red Fish</Title>
        <Price>
          <Amount>19.99</Amount>
          <CurrencyCode>USD</CurrencyCode>
        </Price>
        <Quantity>1</Quantity>
      </Item>
    </Items>
  </Cart>
</Order>';
        #$params = array('merchant' => $merchant, 'documentIdentifier' => $amazonOrderId);

        #echo "doc: <pre>{$doc}</pre><br />\n";
        $doc = base64_encode($doc);
        $params = array('merchant' => $merchant, 'messageType' => 'new order', 'doc' => $doc);

        $options = array('trace' => true, 'timeout' => '10');

        echo '<pre> DEBUG:'."\n";
        print_r($params);
        print_r($options);
        echo '</pre>'."\n";

        echo '<pre>'."\n";
        echo " types:\n";
        print_r($_soap->__getTypes());
        echo " functions:\n";
        print_r($_soap->__getFunctions());
        echo '</pre>'."\n";

        echo '<hr />';


        try {
            $document = $_soap->__soapCall('postDocument', $params, $options);
            #$Result = $_soap->__call('getDocument', $params, $options);

            /*$params = array('merchant' => $merchant, 'messageType' => '_GET_ORDERS_DATA_', 'howMany' => 100);
            $Result = $_soap->__call('getLastNDocumentInfo', $params, $options);*/
        }
        catch (Exception $e) {
            /*print "<pre>\n";
            print "request :\n".htmlspecialchars($_soap->__getLastRequest()) ."\n";
            print "response:\n".htmlspecialchars($_soap->__getLastResponse())."\n";
            print "</pre>";*/

            echo "error: ". $e->getMessage() ."<br />\n";
            echo '<pre> error:'."\n";
            print_r($e);
            echo '</pre>'."\n";
        }

        /*echo '<pre> document:'."\n";
        print_r($document);
        echo '</pre>'."\n";*/
    }

    /**
     * Getting Soap Api object
     *
     * @param   array $options
     * @return  Mage_Cybersource_Model_Api_ExtendedSoapClient
     */
    protected function getSoapApi($options = array())
    {
        #$wsdl = Mage::getBaseDir() . Mage::getStoreConfig('payment/amazonpayments_cba/wsdl');
        $wsdl = Mage::getStoreConfig('payment/amazonpayments_cba/wsdl');
        return new Mage_AmazonPayments_Model_Api_ExtendedSoapClient($wsdl, $options);
    }

    /**
     * Build XML-based Cart for Checkout by Amazon
     *
     * @param Mage_Sales_Model_Quote
     * @return string
     */
    public function getXmlCart(Mage_Sales_Model_Quote $quote)
    {
        $_xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
                .'<Order xmlns="http://payments.amazon.com/checkout/2008-11-30/">'."\n";
        if ($quote->hasItems()) {
            $_xml .= " <Cart>\n"
                    ."   <Items>\n";
            $_taxTable = array();

            foreach ($quote->getAllVisibleItems() as $_item) {
                /*echo '<pre> item:'."\n";
                print_r($_item->getData());
                echo '</pre>'."\n";*/
                $_xml .= "   <Item>\n"
                    ."    <SKU>{$_item->getSku()}</SKU>\n"
                    ."    <MerchantId>{$this->getMerchantId()}</MerchantId>\n"
                    ."    <Title>{$_item->getName()}</Title>\n"
                    ."    <Price>\n"
                    ."     <Amount>{$_item->getPrice()}</Amount>\n"
                    ."     <CurrencyCode>{$quote->getBaseCurrencyCode()}</CurrencyCode>\n"
                    ."    </Price>\n"
                    ."    <Quantity>{$_item->getQty()}</Quantity>\n"
                    ."    <Weight>\n"
                    ."      <Amount>{$_item->getWeight()}</Amount>\n"
                    ."       <Unit>lb</Unit>\n"
                    ."     </Weight>\n"
                    #."     <TaxTableId>tax_{$_item->getSku()}</TaxTableId>\n"
                  /*."     <ShippingMethodIds>\n"
                    ."       <ShippingMethodId>item-shipping-method-1</ShippingMethodId>\n"
                    ."       <ShippingMethodId>item-shipping-method-2</ShippingMethodId>\n"
                    ."       <ShippingMethodId>item-shipping-method-3</ShippingMethodId>\n"
                    ."       <ShippingMethodId>item-shipping-method-4</ShippingMethodId>\n"
                    ."     </ShippingMethodIds>\n"*/
                    ."   </Item>\n";

                    #$_taxTable["tax_{$_item->getSku()}"] = round($_item->getTaxAmount(), 2);
            }
            $_xml .= "   </Items>\n"
                    ." </Cart>\n";
        }

        /*if (count($_taxTable) > 0 && 0) {
                $_xml .= "  <TaxTables>\n";
            foreach ($_taxTable as $_taxTableId => $_taxTableAmount) {
                $_xml .= "    <TaxTable>\n"
                    ."      <TaxTableId>tax_{$_taxTableId}</TaxTableId>\n"
                    ."      <TaxRules>\n"
                    ."        <TaxRule>\n"
                    ."          <Rate>{$_taxTableAmount}</Rate>\n"
                    #."            <USStateRegion>WA</USStateRegion>\n"
                    ."        </TaxRule>\n"
                    ."      </TaxRules>\n"
                    ."    </TaxTable>\n";
            }
            $_xml .= "  </TaxTables>\n";
        }*/

        /*$_xml .= ""
                ." <ShippingMethods>\n"
                ."    <ShippingMethod>\n"
                ."      <ShippingMethodId>item-shipping-method-1</ShippingMethodId>\n"
                ."      <ServiceLevel>Standard</ServiceLevel>\n"
                ."      <Rate>\n"
                ."        <WeightBased>\n"
                ."          <Amount>5.00</Amount>\n"
                ."          <CurrencyCode>USD</CurrencyCode>\n"
                ."        </WeightBased>\n"
                ."      </Rate>\n"
                ."      <IncludedRegions>\n"
                ."        <PredefinedRegion>USAll</PredefinedRegion>\n"
                ."      </IncludedRegions>\n"
                ."    </ShippingMethod>\n"
                ."    <ShippingMethod>\n"
                ."      <ShippingMethodId>item-shipping-method-2</ShippingMethodId>\n"
                ."      <ServiceLevel>Expedited</ServiceLevel>\n"
                ."      <Rate>\n"
                ."        <ItemQuantityBased>\n"
                ."          <Amount>6.00</Amount>\n"
                ."          <CurrencyCode>USD</CurrencyCode>\n"
                ."        </ItemQuantityBased>\n"
                ."      </Rate>\n"
                ."      <IncludedRegions>\n"
                ."        <PredefinedRegion>USAll</PredefinedRegion>\n"
                ."      </IncludedRegions>\n"
                ."    </ShippingMethod>\n"
                ."    <ShippingMethod>\n"
                ."      <ShippingMethodId>item-shipping-method-3</ShippingMethodId>\n"
                ."      <ServiceLevel>TwoDay</ServiceLevel>\n"
                ."      <Rate>\n"
                ."        <ItemQuantityBased>\n"
                ."          <Amount>7.40</Amount>\n"
                ."          <CurrencyCode>USD</CurrencyCode>\n"
                ."        </ItemQuantityBased>\n"
                ."      </Rate>\n"
                ."      <IncludedRegions>\n"
                ."        <PredefinedRegion>USAll</PredefinedRegion>\n"
                ."      </IncludedRegions>\n"
                ."    </ShippingMethod>\n"
                ."    <ShippingMethod>\n"
                ."      <ShippingMethodId>item-shipping-method-4</ShippingMethodId>\n"
                ."      <ServiceLevel>OneDay</ServiceLevel>\n"
                ."      <Rate>\n"
                ."        <ItemQuantityBased>\n"
                ."          <Amount>9.10</Amount>\n"
                ."          <CurrencyCode>USD</CurrencyCode>\n"
                ."        </ItemQuantityBased>\n"
                ."      </Rate>\n"
                ."      <IncludedRegions>\n"
                ."        <PredefinedRegion>USAll</PredefinedRegion>\n"
                ."      </IncludedRegions>\n"
                ."    </ShippingMethod>\n"
                ." </ShippingMethods>\n";*/

        $_xml .= " <IntegratorId>{$this->getIntegratorId()}</IntegratorId>\n"
                ." <IntegratorName>Varien</IntegratorName>\n";
        $_xml .= " <OrderCalculationCallbacks>\n"
                ."   <CalculateTaxRates>false</CalculateTaxRates>\n"
                ."   <CalculatePromotions>false</CalculatePromotions>\n"
                ."   <CalculateShippingRates>true</CalculateShippingRates>\n"
                ."   <OrderCallbackEndpoint>".Mage::getUrl('amazonpayments/cba/callback')."</OrderCallbackEndpoint>\n"
                ."   <ProcessOrderOnCallbackFailure>true</ProcessOrderOnCallbackFailure>\n"
                ." </OrderCalculationCallbacks>\n";
        $_xml .= "</Order>\n";
        #echo $_xml;
        return $_xml;
    }

    /**
     * Handle Callback from CBA and calculate Shipping, Taxes in case XML-based shopping cart
     *
     */
    public function handleXmlCallback($xmlRequest, $session)
    {
        $quoteId = $session->getQuoteId();
        $quote = Mage::getModel('sales/quote')->load($quoteId);

        $storeQuote = Mage::getModel('sales/quote')->setStoreId(Mage::app()->getStore()->getId());

        $storeQuote->merge($session->getQuote());
        $storeQuote
            ->setItemsCount($session->getQuote()->getItemsCount())
            ->setItemsQty($session->getQuote()->getItemsQty())
            ->setChangedFlag(false);
        $storeQuote->save();

        $baseCurrency = $session->getQuote()->getBaseCurrencyCode();
        $currency = Mage::app()->getStore($session->getQuote()->getStoreId())->getBaseCurrency();

        $billingAddress = $quote->getBillingAddress();
        $address = $quote->getShippingAddress();

        #$data = $this->parseRequest($xmlRequest);
        $_address = $this->parseRequestAddress($xmlRequest);
        $this->_address = $_address;

        $regionModel = Mage::getModel('directory/region')->loadByCode($_address['regionCode'], $_address['countryCode']);
        $_regionId = $regionModel->getId();

        $address->setCountryId($_address['countryCode'])
            ->setRegion($_address['regionCode'])
            ->setRegionId($_regionId)
            ->setCity($_address['city'])
            ->setStreet($_address['street'])
            ->setPostcode($_address['postCode']);
        $billingAddress->setCountryId($_address['countryCode'])
            ->setRegion($_address['regionCode'])
            ->setRegionId($_regionId)
            ->setCity($_address['city'])
            ->setStreet($_address['street'])
            ->setPostcode($_address['postCode']);

        $quote->setBillingAddress($billingAddress);
        $quote->setShippingAddress($address);
        $quote->save();

        $address->setCollectShippingRates(true)->collectShippingRates();

        $_carriers = array();
        $carriers = array();
        $errors = array();
        foreach (Mage::getStoreConfig('carriers', $this->getStoreId()) as $carrierCode=>$carrierConfig) {
            if (!isset($carrierConfig['title']) || !$carrierConfig['active']) {
                continue;
            }
            $title = $carrierConfig['title'];
            $_carriers[$carrierCode] = $title;
        }


        $result = Mage::getModel('shipping/shipping')
            ->collectRatesByAddress($address, array_keys($_carriers))
            ->getResult();

        $rates = array();
        $rateCodes = array();
        foreach ($result->getAllRates() as $rate) {
            if ($rate instanceof Mage_Shipping_Model_Rate_Result_Error) {
                $errors[$rate->getCarrierTitle()] = 1;
            } else {
                $_title = $rate->getCarrierTitle().' - '.$rate->getMethodTitle();

                if ($address->getFreeShipping()) {
                    $price = 0;
                } else {
                    $price = $rate->getPrice();
                }

                if ($price) {
                    $price = Mage::helper('tax')->getShippingPrice($price, false, $address);
                }

                $rates[$_title] = $price;
                $rateCodes[$_title] = $rate->getCarrier() . '_' . $rate->getMethod();
                $this->_carriers[] = array(
                    'code' => $rate->getCarrier() . '_' . $rate->getMethod(),
                    'title' => $_title,
                    'price' => $price,
                    'currency' => $currency['currency_code'],
                    );
                unset($errors[$rate->getCarrierTitle()]);
            }
        }

        $xml = $this->_generateXmlResponse($quote);

        $session->getQuote()
            ->setForcedCurrency($currency)
            ->collectTotals()
            ->save();
        return $xml;
    }

    /**
     * Return address from Amazon request
     *
     * @param array $responseArr
     */
    public function parseRequestAddress($xmlResponse)
    {
        $address = array();
        if (strlen(trim($xmlResponse)) > 0) {
            $xml = new Varien_Simplexml_Config();
            $xml->loadString($xmlResponse);

            $address = array(
                'addressId'   => (string) $xml->getNode("CallbackOrders/CallbackOrder/Address/AddressId"),
                'regionCode'  => (string) $xml->getNode("CallbackOrders/CallbackOrder/Address/State"),
                'countryCode' => (string) $xml->getNode("CallbackOrders/CallbackOrder/Address/CountryCode"),
                'city'        => (string) $xml->getNode("CallbackOrders/CallbackOrder/Address/City"),
                'street'      => (string) $xml->getNode("CallbackOrders/CallbackOrder/Address/AddressFieldOne"),
                'postCode'    => (string) $xml->getNode("CallbackOrders/CallbackOrder/Address/PostalCode"),
            );
        } else {
            $address = array(
                'addressId'   => '',
                'regionCode'  => '',
                'countryCode' => '',
                'city'        => '',
                'street'      => '',
                'postCode'    => '',
            );
        }
        return $address;
    }

    /**
     * Generate XML Responce for Amazon with Shipping, Taxes, Promotions
     *
     * @return string xml
     */
    protected function _generateXmlResponse($quote)
    {

        $_xmlString = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<OrderCalculationsRequest xmlns="http://payments.amazon.com/checkout/2008-11-30/">
</OrderCalculationsRequest>
XML;

        $xml = new SimpleXMLElement($_xmlString);

        /*$_carriersTest = array(
            'US Standard' => array(
                    'code'      => 'US Standard',
                    'title'     => 'Standard',
                    'price'     => '3.49',
                    'currency'  => 'USD',
                ),
            'US Expedited' => array(
                    'code'      => 'US Expedited',
                    'title'     => 'Expedited',
                    'price'     => '5.49',
                    'currency'  => 'USD',
                ),
            );*/

        if (count($this->_carriers) > 0) {
            $_xmlResponse = $xml->addChild('Response');
            $_xmlCallbackOrders = $_xmlResponse->addChild('CallbackOrders');
            $_xmlCallbackOrder = $_xmlCallbackOrders->addChild('CallbackOrder');

            $_xmlAddress = $_xmlCallbackOrder->addChild('Address');
            $_xmlAddressId = $_xmlAddress->addChild('AddressId', $this->_address['addressId']);

            $_xmlCallbackOrderItems = $_xmlCallbackOrder->addChild('CallbackOrderItems');
            foreach ($quote->getAllItems() as $_item) {
                $taxAmountTable['tax_'.$_item->getSku()] = $_item->getTaxAmount();

                $_xmlCallbackOrderItem = $_xmlCallbackOrderItems->addChild('CallbackOrderItem');
                $_xmlCallbackOrderItem->addChild('SKU', $_item->getSku());
                #$_xmlCallbackOrderItem->addChild('TaxTableId', 'tax_'.$_item->getSku());
                $_xmlShippingMethodIds = $_xmlCallbackOrderItem->addChild('ShippingMethodIds');
                foreach ($this->_carriers as $_carrier) {
                    $_xmlShippingMethodIds->addChild('ShippingMethodId', $_carrier['code']);
                }

                /*$_xmlShippingMethodIds = $_xmlCallbackOrderItem->addChild('ShippingMethodIds');
                foreach ($_carriersTest as $_carrier) {
                    $_xmlShippingMethodIds->addChild('ShippingMethodId', $_carrier['code']);
                }*/
            }

            /*$_xmlTaxTables = $xml->addChild('TaxTables');
            foreach ($taxAmountTable as $_taxId => $_taxAmount) {
                $_xmlTaxTable = $_xmlTaxTables->addChild('TaxTable');
                $_xmlTaxTable->addChild('TaxTableId', $_taxId);
                $_xmlTaxRules = $_xmlTaxTable->addChild('TaxRules');
                $_xmlTaxRule = $_xmlTaxRules->addChild('TaxRule');
                $_xmlTaxRule->addChild('Rate', $_taxAmount);
                #$_xmlTaxRule->addChild('IsShippingTaxed', 'true');
                #$_xmlTaxRule->addChild('USZipRegion', $this->_address['postCode']);
            }*/

            $_xmlShippingMethods = $xml->addChild('ShippingMethods');
            foreach ($this->_carriers as $_carrier) {
                $_xmlShippingMethod = $_xmlShippingMethods->addChild('ShippingMethod');

                $_xmlShippingMethod->addChild('ShippingMethodId', $_carrier['code']);
                $_xmlShippingMethod->addChild('ServiceLevel', $_carrier['title']);

                $_xmlShippingMethodRate = $_xmlShippingMethod->addChild('Rate');
                $_xmlShippingMethodRateItem = $_xmlShippingMethodRate->addChild('ItemQuantityBased');
                $_xmlShippingMethodRateItem->addChild('Amount', $_carrier['price']);
                $_xmlShippingMethodRateItem->addChild('CurrencyCode', $_carrier['currency']);
            }

            /*foreach ($_carriersTest as $_carrier) {
                $_xmlShippingMethod = $_xmlShippingMethods->addChild('ShippingMethod');

                $_xmlShippingMethod->addChild('ShippingMethodId', $_carrier['code']);
                $_xmlShippingMethod->addChild('ServiceLevel', $_carrier['title']);

                $_xmlShippingMethodRate = $_xmlShippingMethod->addChild('Rate');
                $_xmlShippingMethodRateItem = $_xmlShippingMethodRate->addChild('ItemQuantityBased');
                $_xmlShippingMethodRateItem->addChild('Amount', $_carrier['price']);
                $_xmlShippingMethodRateItem->addChild('CurrencyCode', $_carrier['currency']);
            }*/
        }

        /*echo $xml->asXML();
        echo '<pre> xml:'."\n";
        print_r($xml);
        echo '</pre>'."\n";*/
        return $xml;
    }

    /**
     * Generate XML with error message in case Calculation Callbacks error
     *
     * @param Exception $e
     */
    public function callbackXmlError($e)
    {
        // Posible error codes: INVALID_SHIPPING_ADDRESS | INTERNAL_SERVER_ERROR | SERVICE_UNAVAILABLE
        $xmlErrorString = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<OrderCalculationsResponse xmlns="http://payments.amazon.com/checkout/2008-11-30/">'."\n"
            .' <Response>'."\n"
            .'   <Error>'."\n"
            .'     <Code>INTERNAL_SERVER_ERROR</Code>'."\n"
            .'     <Message>[MESSAGE]</Message>'."\n"
            .'   </Error>'."\n"
            .' </Response>'."\n"
            .'</OrderCalculationsResponse>';

        if ($_errorMsg  = $e->getMessage()) {
            $xmlErrorString = str_replace('[MESSAGE]', $_errorMsg, $xmlErrorString);
        } else {
            $xmlErrorString = str_replace('[MESSAGE]', 'Error', $xmlErrorString);
        }
        $xml = new SimpleXMLElement($xmlErrorString);
        return $xml;
    }

}