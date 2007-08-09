<?php

class Mage_Usa_Model_Shipping_Carrier_Fedex extends Mage_Shipping_Model_Carrier_Abstract
{
    protected $_request = null;
    protected $_result = null;

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!Mage::getStoreConfig('carriers/fedex/active')) {
            return false;
        }
        
        $this->setRequest($request);

        $this->_getXmlQuotes();

        return $this->getResult();
    }
    
    public function setRequest(Mage_Shipping_Model_Rate_Request $request)
    {
        $this->_request = $request;

        $r = new Varien_Object();

/*

        if ($request->getLimitMethod()) {
            $r->setAction($this->getCode('action', 'single'));
            $r->setProduct($request->getLimitMethod());
        } else {
            $r->setAction($this->getCode('action', 'all'));
            $r->setProduct('GNDRES');
        }
        
        if ($request->getUpsPickup()) {
            $pickup = $request->getUpsPickup();
        } else {
            $pickup = Mage::getStoreConfig('carriers/ups/pickup');
        }
        $r->setPickup($this->getCode('pickup', $pickup));
        
        if ($request->getUpsContainer()) {
            $container = $request->getUpsContainer();
        } else {
            $container = Mage::getStoreConfig('carriers/ups/container');
        }
        $r->setContainer($this->getCode('container', $container));
        
        if ($request->getUpsDestType()) {
            $destType = $request->getUpsDestType();
        } else {
            $destType = Mage::getStoreConfig('carriers/ups/dest_type');
        }
        $r->setDestType($this->getCode('dest_type', $destType));
 
*/

        if ($request->getOrigCountry()) {
            $origCountry = $request->getOrigCountry();
        } else {
            $origCountry = Mage::getStoreConfig('shipping/origin/country_id');
        }
        $r->setOrigCountry(Mage::getModel('directory/country')->load($origCountry)->getIso2Code());

        if ($request->getOrigPostcode()) {
            $r->setOrigPostal($request->getOrigPostcode());
        } else {
            $r->setOrigPostal(Mage::getStoreConfig('shipping/origin/postcode'));
        }
        
        if ($request->getDestCountry()) {
            $destCountry = $request->getDestCountry();
        } else {
            $destCountry = 223;
        }
        $r->setDestCountry(Mage::getModel('directory/country')->load($destCountry)->getIso2Code());

        if ($request->getDestPostcode()) {
            $r->setDestPostal($request->getDestPostcode());
        } else {
            $r->setDestPostal('90034');
        }

        $r->setWeight($request->getPackageWeight());
        
        $this->_rawRequest = $r;
        
        return $this;
    }
    
    public function getResult()
    {
       return $this->_result;
    }

    protected function _getXmlQuotes()
    {
        $r = $this->_rawRequest;
/*        
        $params = array(
            'accept_UPS_license_agreement' => 'yes',
            '10_action'      => $r->getAction(),
            '13_product'     => $r->getProduct(),
            '14_origCountry' => $r->getOrigCountry(),
            '15_origPostal'  => $r->getOrigPostal(),
            '19_destPostal'  => $r->getDestPostal(),
            '22_destCountry' => $r->getDestCountry(),
            '23_weight'      => $r->getWeight(),
            '47_rate_chart'  => $r->getPickup(),
            '48_container'   => $r->getContainer(),
            '49_residential' => $r->getDestType(),
        );
*/

// single shipping method request
/*
$xml = new SimpleXMLElement('<FDXRateRequest/>');

$xml->addAttribute('xmlns:api', 'http://www.fedex.com/fsmapi');
$xml->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
$xml->addAttribute('xsi:noNamespaceSchemaLocation', 'FDXRateRequest.xsd');

$requestHeader = $xml->addChild('RequestHeader');
    $requestHeader->addChild('CustomerTransactionIdentifier', 'CTIString');
    $requestHeader->addChild('AccountNumber', '329311708');
    $requestHeader->addChild('MeterNumber', '2436351');
    $requestHeader->addChild('CarrierCode', 'FDXE');

$xml->addChild('ShipDate', '2007-08-14');
$xml->addChild('DropoffType', 'REGULARPICKUP');
$xml->addChild('Service', 'PRIORITYOVERNIGHT');
$xml->addChild('Packaging', 'FEDEXBOX');
$xml->addChild('WeightUnits', 'LBS');
$xml->addChild('Weight', $r->getWeight());

$originAddress = $xml->addChild('OriginAddress');
//    $originAddress->addChild('StateOrProvinceCode', 'GA');
    $originAddress->addChild('PostalCode', $r->getOrigPostal());
    $originAddress->addChild('CountryCode', $r->getOrigCountry());

$destinationAddress = $xml->addChild('DestinationAddress');
//    $destinationAddress->addChild('StateOrProvinceCode', 'GA');
    $destinationAddress->addChild('PostalCode', $r->getDestPostal());
    $destinationAddress->addChild('CountryCode', $r->getDestCountry());

$payment = $xml->addChild('Payment');
    $payment->addChild('PayorType', 'SENDER');
    
$declaredValue = $xml->addChild('DeclaredValue');
    $declaredValue->addChild('Value', '100.00');

$xml->addChild('PackageCount', '1');
 
$request = $xml->asXML();
*/

// multiple shipping method request
$xml = new SimpleXMLElement('<FDXRateAvailableServicesRequest/>');

$xml->addAttribute('xmlns:api', 'http://www.fedex.com/fsmapi');
$xml->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
$xml->addAttribute('xsi:noNamespaceSchemaLocation', 'FDXRateAvailableServicesRequest.xsd');

$requestHeader = $xml->addChild('RequestHeader');
//    $requestHeader->addChild('CustomerTransactionIdentifier', 'CTIString');
    $requestHeader->addChild('AccountNumber', '329311708');
//    $requestHeader->addChild('MeterNumber', '2436351');
    $requestHeader->addChild('MeterNumber', '0');
//    $requestHeader->addChild('CarrierCode', 'FDXE');
//    $requestHeader->addChild('CarrierCode', 'FDXG');
    /*
    FDXE – FedEx Express
    FDXG – FedEx Ground
    */

$xml->addChild('ShipDate', '2007-08-14');
$xml->addChild('ReturnShipmentIndicator', 'NONRETURN');
/*
• NONRETURN
• PRINTRETURNLABEL
• EMAILLABEL
*/
$xml->addChild('DropoffType', 'REGULARPICKUP');
/*
• REGULARPICKUP
• REQUESTCOURIER
• DROPBOX
• BUSINESSSERVICECENTER
• STATION
Only REGULARPICKUP, REQUESTCOURIER, and STATION are
allowed with international freight shipping.
*/
//$xml->addChild('Service', 'PRIORITYOVERNIGHT');
/*
One of the following FedEx Services is optional:
• PRIORITYOVERNIGHT
• STANDARDOVERNIGHT
• FIRSTOVERNIGHT
• FEDEX2DAY
• FEDEXEXPRESSSAVER
• INTERNATIONALPRIORITY
• INTERNATIONALECONOMY
• INTERNATIONALFIRST
• FEDEX1DAYFREIGHT
• FEDEX2DAYFREIGHT
• FEDEX3DAYFREIGHT
• FEDEXGROUND
• GROUNDHOMEDELIVERY
• INTERNATIONALPRIORITY FREIGHT
• INTERNATIONALECONOMY FREIGHT
• EUROPEFIRSTINTERNATIONALPRIORITY
If provided, only that service’s estimated charges will be returned.
*/
$xml->addChild('Packaging', 'YOURPACKAGING');
/*
One of the following package types is required:
• FEDEXENVELOPE
• FEDEXPAK
• FEDEXBOX
• FEDEXTUBE
• FEDEX10KGBOX
• FEDEX25KGBOX
• YOURPACKAGING
If value entered is FEDEXENVELOPE, FEDEX10KGBOX, or
FEDEX25KGBOX, an MPS rate quote is not allowed.
*/
$xml->addChild('WeightUnits', 'LBS');
/*
• LBS
• KGS
LBS is required for a U.S. FedEx Express rate quote.
*/
$xml->addChild('Weight', $r->getWeight());
//$xml->addChild('ListRate', 'true');
/*
Optional.
If = true or 1, list-rate courtesy quotes should be returned in addition to
the discounted quote.
*/

$originAddress = $xml->addChild('OriginAddress');
//    $originAddress->addChild('StateOrProvinceCode', 'GA');   -- ???
    $originAddress->addChild('PostalCode', $r->getOrigPostal());
    $originAddress->addChild('CountryCode', $r->getOrigCountry());

$destinationAddress = $xml->addChild('DestinationAddress');
//    $destinationAddress->addChild('StateOrProvinceCode', 'GA');   -- ???
    $destinationAddress->addChild('PostalCode', $r->getDestPostal());
    $destinationAddress->addChild('CountryCode', $r->getDestCountry());

$payment = $xml->addChild('Payment');
    $payment->addChild('PayorType', 'SENDER');
    /*
    Optional.
    Defaults to SENDER.
    If value other than SENDER is used, no rates will still be returned.
    */

/*
DIMENSIONS

Dimensions / Length 
Optional.
Only applicable if the package type is YOURPACKAGING.
The length of a package.
Format: Numeric, whole number

Dimensions / Width
Optional.
Only applicable if the package type is YOURPACKAGING.
The width of a package.
Format: Numeric, whole number

Dimensions / Height
Optional.
Only applicable if the package type is YOURPACKAGING.
The height of a package.
Format: Numeric, whole number
XML Transaction Layouts: FDXRateAvailableServicesRequest

Dimensions / Units
Required if dimensions are entered.
Only applicable if the package type is YOURPACKAGING.
The valid unit of measure codes for the package dimensions are:
IN – Inches
CM – Centimeters
U.S. FedEx Express must be in inches.
*/

$declaredValue = $xml->addChild('DeclaredValue');
    $declaredValue->addChild('Value', '100.00');
    $declaredValue->addChild('CurrencyCode', 'USD');

$specialServices = $xml->addChild('SpecialServices', 'true');
//    $specialServices->addChild('Alcohol', 'true');
//    $specialServices->addChild('DangerousGoods', 'true')->addChild('Accessibility', 'ACCESSIBLE');
/*
Valid values:
ACCESSIBLE – accessible DG
INACCESSIBLE – inaccessible DG
*/
//    $specialServices->addChild('DryIce', 'true');
//    $specialServices->addChild('ResidentialDelivery', 'true');
/*
If = true or 1, the shipment is Residential Delivery. If Recipient Address
is in a rural area (defined by table lookup), additional charge will be
applied. This element is not applicable to the FedEx Home Delivery
service.
*/
//    $specialServices->addChild('InsidePickup', 'true');
//    $specialServices->addChild('InsideDelivery', 'true');
//    $specialServices->addChild('SaturdayPickup', 'true');
//    $specialServices->addChild('SaturdayDelivery', 'true');
//    $specialServices->addChild('NonstandardContainer', 'true');
//    $specialServices->addChild('SignatureOption', 'true');
/*
Optional.
Specifies the Delivery Signature Option requested for the shipment.
Valid values:
• DELIVERWITHOUTSIGNATURE
• INDIRECT
• DIRECT
• ADULT
For FedEx Express shipments, the DELIVERWITHOUTSIGNATURE
option will not be allowed when the following special services are
requested:
• Alcohol
• Hold at Location
• Dangerous Goods
• Declared Value greater than $500
*/

/*
HOMEDELIVERY

HomeDelivery / Type 
One of the following values are required for FedEx Home Delivery
shipments:
• DATECERTAIN
• EVENING
• APPOINTMENT

PackageCount 
Required for multiple-piece shipments (MPS).
For MPS shipments, 1 piece = 1 box.
For international Freight MPS shipments, this is the total number of
"units." Units are the skids, pallets, or boxes that make up a freight
shipment.
Each unit within a shipment should have its own label.
FDXE only applies to COD, MPS, and international.
Valid values: 1 to 999
*/

/*
VARIABLEHANDLINGCHARGES

VariableHandlingCharges / Level
Optional.
Only applicable if valid Variable Handling Type is present.
Apply fixed or variable handling charges at package or shipment level.
Valid values:
• PACKAGE
• SHIPMENT
The value "SHIPMENT" is applicable only on last piece of FedEx
Ground or FedEx Express MPS shipment only.
Note: Value "SHIPMENT" = shipment level affects the entire shipment.
Anything else sent in Child will be ignored.

VariableHandlingCharges / Type 
Optional.
If valid value is present, a valid Variable Handling Charge is required.
Specifies what type of Variable Handling charges to assess and on
which amount.
Valid values:
• FIXED_AMOUNT
• PERCENTAGE_OF_BASE
• PERCENTAGE_OF_NET
• PERCENTAGE_OF_NET_ EXCL_TAXES

VariableHandlingCharges / AmountOrPercentage
Optional.
Required in conjunction with Variable Handling Type.
Contains the dollar or percentage amount to be added to the Freight
charges. Whether the amount is a dollar or percentage is based on the
Variable Handling Type value that is included in this Request.
Format: Two explicit decimal positions (e.g. 1.00); 10 total length
including decimal place.
*/

$xml->addChild('PackageCount', '1');
 
$request = $xml->asXML();
//echo $request;

/*
        $client = new Zend_Http_Client();
//        $client->setUri(Mage::getStoreConfig('carriers/ups/gateway_url'));
        $client->setUri('http://gateway.fedex.com/GatewayDC');
        $client->setConfig(array('maxredirects'=>0, 'timeout'=>30));
        $client->setParameterPost($request);
        $response = $client->request();
        $responseBody = $response->getBody();
*/

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, 'https://gateway.fedex.com/GatewayDC');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
$responseBody = curl_exec($ch);
curl_close ($ch);

/*
header("content-type:text/xml");
echo($responseBody);
die;
*/

        $this->_parseXmlResponse($responseBody);
    }
    
    protected function _parseXmlResponse($response)
    {
        $xml = simplexml_load_string($response);
        $rArr = array();
        $errorTitle = 'Unknown error';
        if (is_object($xml)) {
            $errorTitle = (is_object($xml->Error) && is_object($xml->Error->Message))?(string)$xml->Error->Message:'Unknown error';
            foreach ($xml->Entry as $entry) {
                $rArr[(string)$entry->Service] = (string)$entry->EstimatedCharges->DiscountedCharges->NetCharge;
            }
            arsort($rArr);
        }

        $result = Mage::getModel('shipping/rate_result');
        $defaults = $this->getDefaults();
        if (empty($rArr)) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('fedex');
            $error->setCarrierTitle(Mage::getStoreConfig('carriers/fedex/title'));
            $error->setErrorMessage($errorTitle);
            $result->append($error);
        } else {
            foreach ($rArr as $method=>$cost) {
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier('fedex');
                $rate->setCarrierTitle(Mage::getStoreConfig('carriers/fedex/title'));
                $rate->setMethod($method);
                $rate->setMethodTitle($this->getCode('method', $method));
                $rate->setCost($cost);
                $rate->setPrice($this->getMethodPrice($cost));
                $result->append($rate);
            }
        }
        $this->_result = $result;
    }
    
    public function getMethodPrice($cost)
    {
        $price = $cost+Mage::getStoreConfig('carriers/fedex/handling');
        return $price;
    }

    public function getCode($type, $code='')
    {
        static $codes = array(
            
            'method'=>array(
                'PRIORITYOVERNIGHT'                => 'Priority Overnight',
                'STANDARDOVERNIGHT'                => 'Standard Overnight',
                'FIRSTOVERNIGHT'                   => 'First Overnight',
                'FEDEX2DAY'                        => '2Day',
                'FEDEXEXPRESSSAVER'                => 'Express Saver',
                'INTERNATIONALPRIORITY'            => 'International Priority',
                'INTERNATIONALECONOMY'             => 'International Economy',
                'INTERNATIONALFIRST'               => 'International First',
                'FEDEX1DAYFREIGHT'                 => '1 Day Freight',
                'FEDEX2DAYFREIGHT'                 => '2 Day Freight',
                'FEDEX3DAYFREIGHT'                 => '3 Day Freight',
                'FEDEXGROUND'                      => 'Ground',
                'GROUNDHOMEDELIVERY'               => 'Home Delivery',
                'INTERNATIONALPRIORITY FREIGHT'    => 'Intl Priority Freight',
                'INTERNATIONALECONOMY FREIGHT'     => 'Intl Economy Freight',
                'EUROPEFIRSTINTERNATIONALPRIORITY' => 'Europe First Priority',
            ),
            
            'dropoff'=>array(
                'REGULARPICKUP'         => 'Regular Pickup',
                'REQUESTCOURIER'        => 'Request Courier',
                'DROPBOX'               => 'Drop Box',
                'BUSINESSSERVICECENTER' => 'Business Service Center',
                'STATION'               => 'Station',
            ),
            
            'packaging'=>array(
                'FEDEXENVELOPE' => 'FedEx Envelope',
                'FEDEXPAK'      => 'FedEx Pak',
                'FEDEXBOX'      => 'FedEx Box',
                'FEDEXTUBE'     => 'FedEx Tube',
                'FEDEX10KGBOX'  => 'FedEx 10kg Box',
                'FEDEX25KGBOX'  => 'FedEx 25kg Box',
                'YOURPACKAGING' => 'Your Packaging',
            ),
            
        );
        
        if (!isset($codes[$type])) {
            throw Mage::exception('Mage_Shipping', 'Invalid FedEx XML code type: '.$type);
        }
        
        if (''===$code) {
            return $codes[$type];
        }
        
        if (!isset($codes[$type][$code])) {
            throw Mage::exception('Mage_Shipping', 'Invalid FedEx XML code for type '.$type.': '.$code);
        }
        
        return $codes[$type][$code];
    }

}