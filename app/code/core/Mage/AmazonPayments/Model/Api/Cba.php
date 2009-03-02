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
     * Build XML-based Cart for Checkout by Amazon
     *
     * @param Mage_Sales_Model_Quote
     * @return string
     */
    public function getXmlCart(Mage_Sales_Model_Quote $quote)
    {
        $_xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
                .'<Order xmlns="http://payments.amazon.com/checkout/2008-11-30/">'."\n";
        if (!$quote->hasItems()) {
            return false;
        }
        $_xml .= " <ClientRequestId>{$quote->getId()}</ClientRequestId>\n"; // Returning parametr
        #        ."<ExpirationDate></ExpirationDate>";

        $_xml .= " <Cart>\n"
                ."   <Items>\n";
        $_taxTable = array();

        foreach ($quote->getAllVisibleItems() as $_item) {
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
                ."     </Weight>\n";
            if (!$this->getConfigData('use_callback_api')) {
                $_xml .= "     <TaxTableId>tax_{$_item->getSku()}</TaxTableId>\n"
                    ."     <ShippingMethodIds>\n"
                    ."       <ShippingMethodId>US Standard</ShippingMethodId>\n"
                    ."       <ShippingMethodId>US Expedited</ShippingMethodId>\n"
                    ."       <ShippingMethodId>US Two-Day</ShippingMethodId>\n"
                    ."       <ShippingMethodId>US One-Day</ShippingMethodId>\n"
                    ."     </ShippingMethodIds>\n";
            }
            $_xml .= "   </Item>\n";


            $_taxTable["{$_item->getSku()}"] = round($_item->getTaxPercent()/100, 4);
            #$_taxTable["{$_item->getSku()}"] = round($_item->getTaxAmount(), 2);
        }
        $_xml .= "   </Items>\n"
                ." </Cart>\n";

        if (!$this->getConfigData('use_callback_api')) {
            if (count($_taxTable) > 0) {
                    $_xml .= " <TaxTables>\n";
                foreach ($_taxTable as $_taxTableId => $_taxTableValue) {
                    $_xml .= "    <TaxTable>\n"
                        ."      <TaxTableId>tax_{$_taxTableId}</TaxTableId>\n"
                        ."      <TaxRules>\n"
                        ."        <TaxRule>\n"
                        ."          <Rate>{$_taxTableValue}</Rate>\n"
                        #."            <USStateRegion>WA</USStateRegion>\n"
                        ."          <PredefinedRegion>USAll</PredefinedRegion>\n"
                        ."        </TaxRule>\n"
                        ."      </TaxRules>\n"
                        ."    </TaxTable>\n";
                }
                $_xml .= " </TaxTables>\n";
            }

            $_xml .= ""
                    ." <ShippingMethods>\n"
                    ."    <ShippingMethod>\n"
                    ."      <ShippingMethodId>US Standard</ShippingMethodId>\n"
                    ."      <ServiceLevel>Standard</ServiceLevel>\n"
                    ."      <Rate>\n"
                    ."        <ItemQuantityBased>\n"
                    ."          <Amount>5.00</Amount>\n"
                    ."          <CurrencyCode>USD</CurrencyCode>\n"
                    ."        </ItemQuantityBased>\n"
                    ."      </Rate>\n"
                    ."      <IncludedRegions>\n"
                    ."        <PredefinedRegion>USAll</PredefinedRegion>\n"
                    ."      </IncludedRegions>\n"
                    ."    </ShippingMethod>\n"
                    ."    <ShippingMethod>\n"
                    ."      <ShippingMethodId>US Expedited</ShippingMethodId>\n"
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
                    ."      <ShippingMethodId>US Two-Day</ShippingMethodId>\n"
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
                    ."      <ShippingMethodId>US One-Day</ShippingMethodId>\n"
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
                    ." </ShippingMethods>\n";
        }

        $_xml .= " <IntegratorId>{$this->getIntegratorId()}</IntegratorId>\n"
                ." <IntegratorName>Varien</IntegratorName>\n";
        $_xml .= " <OrderCalculationCallbacks>\n"
                ."   <CalculateTaxRates>false</CalculateTaxRates>\n"
                ."   <CalculatePromotions>false</CalculatePromotions>\n"
                ."   <CalculateShippingRates>true</CalculateShippingRates>\n"
                ."   <OrderCallbackEndpoint>".Mage::getUrl('amazonpayments/cba/callback')."</OrderCallbackEndpoint>\n"
                ."   <ProcessOrderOnCallbackFailure>true</ProcessOrderOnCallbackFailure>\n"
                ." </OrderCalculationCallbacks>\n";

        #$_xml .= "<ReturnUrl>anyURI</ReturnUrl>"
        #        ."<CancelUrl>anyURI</CancelUrl>"
        #        ."<YourAccountUrl>anyURI</YourAccountUrl>";

        $_xml .= "</Order>\n";
        return $_xml;
    }

    /**
     * Handle Callback from CBA and calculate Shipping, Taxes in case XML-based shopping cart
     *
     */
    public function handleXmlCallback($xmlRequest, $session)
    {
        $_address = $this->_parseRequestAddress($xmlRequest);

        #$quoteId = $session->getAmazonQuoteId();
        $quoteId = $_address['ClientRequestId'];
        $quote = Mage::getModel('sales/quote')->load($quoteId);

        $baseCurrency = $session->getQuote()->getBaseCurrencyCode();
        $currency = Mage::app()->getStore($session->getQuote()->getStoreId())->getBaseCurrency();

        $billingAddress = $quote->getBillingAddress();
        $address = $quote->getShippingAddress();

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

        $_items = $this->_parseRequestItems($xmlRequest);
        $xml = $this->_generateXmlResponse($quote, $_items);

        $session->getQuote()
            ->setForcedCurrency($currency)
            ->collectTotals()
            ->save();
        return $xml;
    }

    /**
     * Parse request from Amazon and return order details
     *
     * @param string xml
     */
    public function parseOrder($xmlData)
    {
        $parsedOrder = array();
        if (strlen(trim($xmlData)) > 0) {
            $xml = new Varien_Simplexml_Config();
            $xml->loadString($xmlData);
            $parsedOrder = array(
                'NotificationReferenceId'   => (string) $xml->getNode("NotificationReferenceId"),
                'amazonOrderID'     => (string) $xml->getNode("ProcessedOrder/AmazonOrderID"),
                'orderDate'         => (string) $xml->getNode("ProcessedOrder/OrderDate"),
                'orderChannel'      => (string) $xml->getNode("ProcessedOrder/OrderChannel"),
                'buyerName'         => (string) $xml->getNode("ProcessedOrder/BuyerInfo/BuyerName"),
                'buyerEmailAddress' => (string) $xml->getNode("ProcessedOrder/BuyerInfo/BuyerEmailAddress"),
                'ShippingLevel'     => (string) $xml->getNode("ProcessedOrder/ShippingServiceLevel"),
                'shippingAddress'   => array(
                    'name'          => (string) $xml->getNode("ProcessedOrder/ShippingAddress/Name"),
                    'street'        => (string) $xml->getNode("ProcessedOrder/ShippingAddress/AddressFieldOne"),
                    'city'          => (string) $xml->getNode("ProcessedOrder/ShippingAddress/City"),
                    'regionCode'    => (string) $xml->getNode("ProcessedOrder/ShippingAddress/State"),
                    'postCode'      => (string) $xml->getNode("ProcessedOrder/ShippingAddress/PostalCode"),
                    'countryCode'   => (string) $xml->getNode("ProcessedOrder/ShippingAddress/CountryCode"),
                ),
                'items'             => array(),
            );

            $_total = $_shipping = $_tax = $_shippingTax = $_subtotalPromo = $_shippingPromo = $_subtotal = 0;
            $_itemsCount = $_itemsQty = 0;
            foreach ($xml->getNode("ProcessedOrder/ProcessedOrderItems/ProcessedOrderItem") as $_item) {
                $parsedOrder['ClientRequestId'] = (string) $_item->ClientRequestId;
                $_sku = (string) $_item->SKU;
                $_itemQty = (string) $_item->Quantity;
                $_itemsQty += $_itemQty;
                $_itemsCount++;
                $parsedOrder['items'][$_sku] = array(
                    'sku'   => $_sku,
                    'title' => (string) $_item->Title,
                    'price' => array(
                        'amount'       => (string) $_item->Price->Amount,
                        'currencyCode' => (string) $_item->Price->CurrencyCode,
                        ),
                    'qty' => $_itemQty,
                    'weight' => array(
                        'amount' => (string) $_item->Weight->Amount,
                        'unit'   => (string) $_item->Weight->Unit,
                        ),
                );
                $_itemSubtotal = 0;
                foreach ($_item->ItemCharges->Component as $_component) {
                    switch ((string) $_component->Type) {
                        case 'Principal':
                            $_itemSubtotal  += (string) $_component->Charge->Amount;
                            $parsedOrder['items'][$_sku]['subtotal'] = $_itemSubtotal;
                            break;
                        case 'Shipping':
                            $_shipping      += (string) $_component->Charge->Amount;
                            break;
                        case 'Tax':
                            $_tax           += (string) $_component->Charge->Amount;
                            break;
                        case 'ShippingTax':
                            $_shippingTax   += (string) $_component->Charge->Amount;
                            break;
                        case 'PrincipalPromo':
                            $_subtotalPromo += (string) $_component->Charge->Amount;
                            break;
                        case 'ShippingPromo':
                            $_shippingPromo += (string) $_component->Charge->Amount;
                            break;
                    }
                }
                $_subtotal += $_itemSubtotal;
            }

            $parsedOrder['itemsCount'] = $_itemsCount;
            $parsedOrder['itemsQty'] = $_itemsQty;

            $parsedOrder['subtotal'] = $_subtotal;
            $parsedOrder['shippingAmount'] = $_shipping;
            $parsedOrder['tax'] = $_tax + $_shippingTax;
            $parsedOrder['shippingTax'] = $_shippingTax;
            $parsedOrder['discount'] = $_subtotalPromo + $_shippingPromo;
            $parsedOrder['discountShipping'] = $_shippingPromo;

            $parsedOrder['total'] = $_subtotal + $_shipping + $_tax + $_shippingTax - abs($_subtotalPromo) - abs($_shippingPromo);
        }
        return $parsedOrder;
    }

    /**
     * Return address from Amazon request
     *
     * @param array $responseArr
     */
    protected function _parseRequestAddress($xmlResponse)
    {
        $address = array();
        if (strlen(trim($xmlResponse)) > 0) {
            $xml = new Varien_Simplexml_Config();
            $xml->loadString($xmlResponse);

            $address = array(
                'addressId'         => (string) $xml->getNode("CallbackOrders/CallbackOrder/Address/AddressId"),
                'regionCode'        => (string) $xml->getNode("CallbackOrders/CallbackOrder/Address/State"),
                'countryCode'       => (string) $xml->getNode("CallbackOrders/CallbackOrder/Address/CountryCode"),
                'city'              => (string) $xml->getNode("CallbackOrders/CallbackOrder/Address/City"),
                'street'            => (string) $xml->getNode("CallbackOrders/CallbackOrder/Address/AddressFieldOne"),
                'postCode'          => (string) $xml->getNode("CallbackOrders/CallbackOrder/Address/PostalCode"),
                'ClientRequestId'   => (string) $xml->getNode("ClientRequestId"),
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
     * Return items SKUs from Amazon request
     *
     * @param array $responseArr
     */
    protected function _parseRequestItems($xmlResponse)
    {
        $items = array();
        if (strlen(trim($xmlResponse)) > 0) {
            $xml = new Varien_Simplexml_Config();
            $xml->loadString($xmlResponse);
            $itemsXml = $xml->getNode("Cart/Items");

            foreach ($itemsXml as $_item) {
                $_itemArr = $_item->asArray();
                $items[$_itemArr['Item']['SKU']] = $_itemArr['Item']['SKU'];
            }
        } else {
            return false;
        }
        return $items;
    }

    /**
     * Generate XML Responce for Amazon with Shipping, Taxes, Promotions
     *
     * @return string xml
     */
    protected function _generateXmlResponse($quote, $items = array())
    {

        $_xmlString = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<OrderCalculationsResponse xmlns="http://payments.amazon.com/checkout/2008-11-30/">
</OrderCalculationsResponse>
XML;

        $xml = new SimpleXMLElement($_xmlString);

        $_carriersAmazon = array(
            'Standard' => array(
                    'code'      => 'US Standard',
                    'title'     => 'Standard',
                    'price'     => '3.49',
                    'currency'  => 'USD',
                ),
            'Expedited' => array(
                    'code'      => 'US Expedited',
                    'title'     => 'Expedited',
                    'price'     => '5.49',
                    'currency'  => 'USD',
                ),
            'TwoDay' => array(
                    'code'      => 'US Two-Day',
                    'title'     => 'TwoDay',
                    'price'     => '6.49',
                    'currency'  => 'USD',
                ),
            'OneDay' => array(
                    'code'      => 'US One-Day',
                    'title'     => 'OneDay',
                    'price'     => '7.49',
                    'currency'  => 'USD',
                ),
            );

        if (count($this->_carriers) > 0) {
            $_xmlResponse = $xml->addChild('Response');
            $_xmlCallbackOrders = $_xmlResponse->addChild('CallbackOrders');
            $_xmlCallbackOrder = $_xmlCallbackOrders->addChild('CallbackOrder');

            $_xmlAddress = $_xmlCallbackOrder->addChild('Address');
            $_xmlAddressId = $_xmlAddress->addChild('AddressId', $this->_address['addressId']);

            $_xmlCallbackOrderItems = $_xmlCallbackOrder->addChild('CallbackOrderItems');
            foreach ($items as $_itemSku) {
                #$taxAmountTable['tax_'.$_itemSku] = $_item->getTaxAmount();

                $_xmlCallbackOrderItem = $_xmlCallbackOrderItems->addChild('CallbackOrderItem');
                $_xmlCallbackOrderItem->addChild('SKU', $_itemSku);
                #$_xmlCallbackOrderItem->addChild('TaxTableId', 'tax_'.$_item->getSku());

                $_xmlShippingMethodIds = $_xmlCallbackOrderItem->addChild('ShippingMethodIds');
                /*foreach ($this->_carriers as $_carrier) {
                    $_xmlShippingMethodIds->addChild('ShippingMethodId', $_carrier['code']);
                }*/
                foreach ($_carriersAmazon as $_carrier) {
                    $_xmlShippingMethodIds->addChild('ShippingMethodId', $_carrier['code']);
                }
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
            /*foreach ($this->_carriers as $_carrier) {
                $_xmlShippingMethod = $_xmlShippingMethods->addChild('ShippingMethod');

                $_xmlShippingMethod->addChild('ShippingMethodId', $_carrier['code']);
                $_xmlShippingMethod->addChild('ServiceLevel', $_carrier['title']);

                $_xmlShippingMethodRate = $_xmlShippingMethod->addChild('Rate');
                $_xmlShippingMethodRateItem = $_xmlShippingMethodRate->addChild('ItemQuantityBased');
                $_xmlShippingMethodRateItem->addChild('Amount', $_carrier['price']);
                $_xmlShippingMethodRateItem->addChild('CurrencyCode', $_carrier['currency']);
            }*/
            foreach ($_carriersAmazon as $_carrier) {
                $_xmlShippingMethod = $_xmlShippingMethods->addChild('ShippingMethod');

                $_xmlShippingMethod->addChild('ShippingMethodId', $_carrier['code']);
                $_xmlShippingMethod->addChild('ServiceLevel', $_carrier['title']);

                $_xmlShippingMethodRate = $_xmlShippingMethod->addChild('Rate');
                // Posible values: ShipmentBased | WeightBased | ItemQuantityBased
                $_xmlShippingMethodRateItem = $_xmlShippingMethodRate->addChild('ItemQuantityBased');
                $_xmlShippingMethodRateItem->addChild('Amount', $_carrier['price']);
                $_xmlShippingMethodRateItem->addChild('CurrencyCode', $_carrier['currency']);

                $_xmlShippingMethodIncludedRegions = $_xmlShippingMethod->addChild('IncludedRegions');
                #$_xmlShippingMethodIncludedRegions->addChild('PredefinedRegion', 'USAll');
                $_xmlShippingMethodIncludedRegions->addChild('USZipRegion', '10001');
            }
        }

        return $xml;
    }

    /**
     * Generate XML with error message in case Calculation Callbacks error
     *
     * @param Exception $e
     */
    public function callbackXmlError(Exception $e)
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

        $_errorMessage = "{$_errorMsg}\n\n"
            ."code: {$e->getCode()}\n\n"
            ."file: {$e->getFile()}\n\n"
            ."line: {$e->getLine()}\n\n"
            ."trac: {$e->getTraceAsString()}\n\n";
        if ($this->getDebug()) {
         $debug = Mage::getModel('amazonpayments/api_debug')
            ->setResponseBody($_errorMessage)
            ->setRequestBody(time() .' - error callback response')
            ->save();
        }

        if ($_errorMsg = $e->getMessage() && 0) {
            $xmlErrorString = str_replace('[MESSAGE]', $_errorMsg, $xmlErrorString);
        } else {
            $xmlErrorString = str_replace('[MESSAGE]', 'Error', $xmlErrorString);
        }
        $xml = new SimpleXMLElement($xmlErrorString);
        return $xml;
    }

}