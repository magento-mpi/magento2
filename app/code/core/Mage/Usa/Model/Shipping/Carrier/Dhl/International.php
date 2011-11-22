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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * DHL International (API v1.4)
 *
 * @category Mage
 * @package  Mage_Usa
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Mage_Usa_Model_Shipping_Carrier_Dhl_International
    extends Mage_Usa_Model_Shipping_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    /**
     * Carrier Product indicator
     */
    const DOC_DOC        = 'D';
    const DOC_NON_DOC    = 'N';
    const DOC_EVERYTHING = 'A';

    /**
     * Code of the carrier
     *
     * @var string
     */
    const CODE = 'dhlint';
    protected $_code = self::CODE;

    /**
     * Rate request data
     *
     * @var Mage_Shipping_Model_Rate_Request|null
     */
    protected $_request = null;

    /**
     * Raw rate request data
     *
     * @var Varien_Object|null
     */
    protected $_rawRequest = null;

    /**
     * Rate result data
     *
     * @var Mage_Shipping_Model_Rate_Result|null
     */
    protected $_result = null;

    /**
     * Countries parameters data
     *
     * @var SimpleXMLElement|null
     */
    protected $_countryParams = null;

    /**
     * Errors placeholder
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Dhl rates result
     *
     * @var array
     */
    protected $_rates = array();

    /**
     * Collect and get rates
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return bool|Mage_Shipping_Model_Rate_Result|null
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag($this->_activeFlag)) {
            return false;
        }

        $requestDhl = clone $request;
        $origCompanyName = $requestDhl->getOrigCompanyName();
        if (!$origCompanyName) {
            $origCompanyName = Mage::getStoreConfig(
                Mage_Core_Model_Store::XML_PATH_STORE_STORE_NAME,
                $requestDhl->getStoreId()
            );
        }

        $origCountryId = $requestDhl->getOrigCountryId();
        if (!$origCountryId) {
            $origCountryId = Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
                $requestDhl->getStoreId()
            );
        }
        $origState = $requestDhl->getOrigState();
        if (!$origState) {
            $origState = Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_REGION_ID,
                $requestDhl->getStoreId()
            );
        }
        $origCity = $requestDhl->getOrigCity();
        if (!$origCity) {
            $origCity = Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_CITY,
                $requestDhl->getStoreId()
            );
        }

        $origPostcode = $requestDhl->getOrigPostcode();
        if (!$origPostcode) {
            $origPostcode = Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_ZIP,
                $requestDhl->getStoreId()
            );
        }
        $requestDhl->setOrigCompanyName($origCompanyName)
            ->setCountryId($origCountryId)
            ->setOrigState($origState)
            ->setOrigCity($origCity)
            ->setOrigPostal($origPostcode);
        $this->setRequest($requestDhl);
        $this->_result = $this->_getQuotes();
        $this->_updateFreeMethodQuote($request);

        return $this->getResult();
    }

    /**
     * Returns request result
     *
     * @return Mage_Shipping_Model_Rate_Result|null
     */
    public function getResult()
    {
        return $this->_result;
    }

    /**
     * Prepare and set request in property of current instance
     *
     * @param Varien_Object $request
     * @return Mage_Usa_Model_Shipping_Carrier_Dhl
     */
    public function setRequest(Varien_Object $request)
    {
        $this->_request = $request;

        $requestObject = new Varien_Object();

        if ($request->getAction() == 'GenerateLabel') {
            $requestObject->setAction('GenerateLabel');
        } else {
            $requestObject->setAction('RateEstimate');
        }
        $requestObject->setIsGenerateLabelReturn($request->getIsGenerateLabelReturn());

        $requestObject->setStoreId($request->getStoreId());

        if ($request->getLimitMethod()) {
            $requestObject->setService($request->getLimitMethod());
        }

        if ($request->getDhlId()) {
            $id = $request->getDhlId();
        } else {
            $id = $this->getConfigData('id');
        }
        $requestObject->setId($id);

        if ($request->getDhlPassword()) {
            $password = $request->getDhlPassword();
        } else {
            $password = $this->getConfigData('password');
        }
        $requestObject->setPassword($password);

        if ($request->getDhlAccount()) {
            $accountNbr = $request->getDhlAccount();
        } else {
            $accountNbr = $this->getConfigData('account');
        }
        $requestObject->setAccountNbr($accountNbr);

        if ($request->getDhlShippingKey()) {
            $shippingKey = $request->getDhlShippingKey();
        } else {
            $shippingKey = $this->getConfigData('shipping_key');
        }
        $requestObject->setShippingKey($shippingKey);

        if ($request->getDhlShippingIntlKey()) {
            $shippingKey = $request->getDhlShippingIntlKey();
        } else {
            $shippingKey = $this->getConfigData('shipping_intlkey');
        }
        $requestObject->setShippingIntlKey($shippingKey);

        if ($request->getDhlShipmentType()) {
            $shipmentType = $request->getDhlShipmentType();
        } else {
            $shipmentType = $this->getConfigData('shipment_type');
        }
        $requestObject->setShipmentType($shipmentType);

        if ($request->getDhlDutiable()) {
            $shipmentDutible = $request->getDhlDutiable();
        } else {
            $shipmentDutible = $this->getConfigData('dutiable');
        }
        $requestObject->setDutiable($shipmentDutible);

        if ($request->getDhlDutyPaymentType()) {
            $dutypaytype = $request->getDhlDutyPaymentType();
        } else {
            $dutypaytype = $this->getConfigData('dutypaymenttype');
        }
        $requestObject->setDutyPaymentType($dutypaytype);

        if ($request->getDhlContentDesc()) {
            $contentdesc = $request->getDhlContentDesc();
        } else {
            $contentdesc = $this->getConfigData('contentdesc');
        }
        $requestObject->setContentDesc($contentdesc);

        if ($request->getDestPostcode()) {
            $requestObject->setDestPostal($request->getDestPostcode());
        }

        if ($request->getOrigCountry()) {
            $origCountry = $request->getOrigCountry();
        } else {
            $origCountry = Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
                $requestObject->getStoreId()
            );
        }
        $requestObject->setOrigCountry($origCountry);

        if ($request->getOrigCountryId()) {
            $origCountryId = $request->getOrigCountryId();
        } else {
            $origCountryId = Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
                $requestObject->getStoreId()
            );
        }
        $requestObject->setOrigCountryId($origCountryId);

        if ($request->getAction() == 'GenerateLabel') {
            $packageParams = $request->getPackageParams();
            $shippingWeight = $request->getPackageWeight();
            if ($packageParams->getWeightUnits() != Zend_Measure_Weight::POUND) {
                $shippingWeight = round(Mage::helper('usa')->convertMeasureWeight(
                    $request->getPackageWeight(),
                    $packageParams->getWeightUnits(),
                    Zend_Measure_Weight::POUND
                ));
            }
            if ($packageParams->getDimensionUnits() != Zend_Measure_Length::INCH) {
                $packageParams->setLength(round(Mage::helper('usa')->convertMeasureDimension(
                    $packageParams->getLength(),
                    $packageParams->getDimensionUnits(),
                    Zend_Measure_Length::INCH
                )));
                $packageParams->setWidth(round(Mage::helper('usa')->convertMeasureDimension(
                    $packageParams->getWidth(),
                    $packageParams->getDimensionUnits(),
                    Zend_Measure_Length::INCH
                )));
                $packageParams->setHeight(round(Mage::helper('usa')->convertMeasureDimension(
                    $packageParams->getHeight(),
                    $packageParams->getDimensionUnits(),
                    Zend_Measure_Length::INCH
                )));
            }
            $requestObject->setPackageParams($packageParams);
        } else {
            /*
            * DHL only accepts weight as a whole number. Maximum length is 3 digits.
            */
            $shippingWeight = $request->getPackageWeight();
            $weight = $this->getTotalNumOfBoxes($shippingWeight);
            $shippingWeight = round(max(1, $weight), 0);
        }

        $requestObject->setValue(round($request->getPackageValue(), 2));
        $requestObject->setValueWithDiscount($request->getPackageValueWithDiscount());
        $requestObject->setCustomsValue($request->getPackageCustomsValue());
        $requestObject->setDestStreet(
            Mage::helper('core/string')->substr(str_replace("\n", '', $request->getDestStreet()), 0, 35));
        $requestObject->setDestStreetLine2($request->getDestStreetLine2());
        $requestObject->setDestCity($request->getDestCity());
        $requestObject->setOrigCompanyName($request->getOrigCompanyName());
        $requestObject->setOrigCity($request->getOrigCity());
        $requestObject->setOrigPhoneNumber($request->getOrigPhoneNumber());
        $requestObject->setOrigPersonName($request->getOrigPersonName());
        $requestObject->setOrigEmail(
            Mage::getStoreConfig('trans_email/ident_general/email', $requestObject->getStoreId()));
        $requestObject->setOrigCity($request->getOrigCity());
        $requestObject->setOrigPostal($request->getOrigPostal());
        $originStreet2 = Mage::getStoreConfig(
            Mage_Shipping_Model_Shipping::XML_PATH_STORE_ADDRESS2, $requestObject->getStoreId());
        $requestObject->setOrigStreet($request->getOrigStreet() ? $request->getOrigStreet() : $originStreet2);
        $requestObject->setOrigStreetLine2($request->getOrigStreetLine2());
        $requestObject->setDestPhoneNumber($request->getDestPhoneNumber());
        $requestObject->setDestPersonName($request->getDestPersonName());
        $requestObject->setDestCompanyName($request->getDestCompanyName());


        if (is_numeric($request->getOrigState())) {
            $requestObject->setOrigState(Mage::getModel('directory/region')->load($request->getOrigState())->getCode());
        } else {
            $requestObject->setOrigState($request->getOrigState());
        }

        if ($request->getDestCountryId()) {
            $destCountry = $request->getDestCountryId();
        } else {
            $destCountry = self::USA_COUNTRY_ID;
        }

        // for DHL, Puerto Rico state for US will assume as Puerto Rico country
        // for Puerto Rico, dhl will ship as international
        if ($destCountry == self::USA_COUNTRY_ID && ($request->getDestPostcode() == '00912'
                                                     || $request->getDestRegionCode() == self::PUERTORICO_COUNTRY_ID)
        ) {
            $destCountry = self::PUERTORICO_COUNTRY_ID;
        }

        $requestObject->setDestCountryId($destCountry);
        $requestObject->setDestState($request->getDestRegionCode());

        $requestObject->setWeight($shippingWeight);
        $requestObject->setFreeMethodWeight($request->getFreeMethodWeight());

        $requestObject->setOrderShipment($request->getOrderShipment());

        if ($request->getPackageId()) {
            $requestObject->setPackageId($request->getPackageId());
        }

        $this->_rawRequest = $requestObject;
        return $this;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        $contentType = $this->getConfigData('content_type');
        $allowedMethods = '';
        switch ($contentType) {
            case self::DOC_DOC:
                $allowedMethods = explode(',', $this->getConfigData('doc_methods'));
                break;

            case self::DOC_NON_DOC:
                $allowedMethods = explode(',', $this->getConfigData('nondoc_methods'));
                break;

            default:
                Mage::throwException('Content type is wrong. See DOC_* constants.');
        }
        $methods = array();
        foreach ($allowedMethods as $k) {
            $methods[$k] = $this->getDhlProductDescription($k);
        }
        return $methods;
    }

    /**
     * Returns DHL shipment methods (depending on package content type, if necessary)
     *
     * @param string $doc Package content type (doc/non-doc) see DOC_* constants
     * @return array
     */
    public function getDhlProducts($doc = self::DOC_EVERYTHING)
    {
        $helper = Mage::helper('usa');

        // Documents shipping
        $docProducts = array(
            '2' => $helper->__('Easy shop'),
            '5' => $helper->__('Sprintline'),
            '6' => $helper->__('Secureline'),
            '7' => $helper->__('Express easy'),
            '9' => $helper->__('Europack'),
            'B' => $helper->__('Break bulk express'),
            'C' => $helper->__('Medical express'),
            'D' => $helper->__('Express worldwide'), // product content code: DOX
            'U' => $helper->__('Express worldwide'), // product content code: ECX
            'K' => $helper->__('Express 9:00'),
            'L' => $helper->__('Express 10:30'),
            'G' => $helper->__('Domestic economy select'),
            'W' => $helper->__('Economy select'),
            'I' => $helper->__('Break bulk economy'),
            'N' => $helper->__('Domestic express'),
            'O' => $helper->__('Others'),
            'R' => $helper->__('Globalmail business'),
            'S' => $helper->__('Same day'),
            'T' => $helper->__('Express 12:00'),
            'X' => $helper->__('Express envelope'),
        );

        // Services for shipping non-documents cargo
        $nonDocProducts = array(
            '1' => $helper->__('Customer services'),
            '3' => $helper->__('Easy shop'),
            '4' => $helper->__('Jetline'),
            '8' => $helper->__('Express easy'),
            'P' => $helper->__('Express worldwide'),
            'Q' => $helper->__('Medical express'),
            'E' => $helper->__('Express 9:00'),
            'F' => $helper->__('Freight worldwide'),
            'H' => $helper->__('Economy select'),
            'J' => $helper->__('Jumbo box'),
            'M' => $helper->__('Express 10:30'),
            'V' => $helper->__('Europack'),
            'Y' => $helper->__('Express 12:00'),
        );

        switch ($doc) {
            case self::DOC_DOC:
                $products = $docProducts;
                break;
            case self::DOC_NON_DOC:
                $products = $nonDocProducts;
                break;
            default:
                $products = array_merge($docProducts, $nonDocProducts);
        }

        return $products;
    }

    /**
     * Returns description of DHL shipping method by its code
     *
     * @param string $code One-symbol code (see getDhlProducts())
     * @return bool
     */
    public function getDhlProductDescription($code)
    {
        $dhlProducts = $this->getDhlProducts();
        return isset($dhlProducts[$code]) ? $dhlProducts[$code] : false;
    }

    /**
     * Get shipping quotes
     *
     * @return Mage_Core_Model_Abstract|Mage_Shipping_Model_Rate_Result
     */
    protected function _getQuotes()
    {
        $rawRequest = $this->_rawRequest;
        $xmlStr = '<?xml version = "1.0" encoding = "UTF-8"?>'
                . '<p:DCTRequest xmlns:p="http://www.dhl.com" xmlns:p1="http://www.dhl.com/datatypes" '
                . 'xmlns:p2="http://www.dhl.com/DCTRequestdatatypes" '
                . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                . 'xsi:schemaLocation="http://www.dhl.com DCT-req.xsd "/>';
        $xml = new SimpleXMLElement($xmlStr);
        $nodeGetQuote = $xml->addChild('GetQuote', '', '');
        $nodeRequest = $nodeGetQuote->addChild('Request');

        $nodeServiceHeader = $nodeRequest->addChild('ServiceHeader');
        $nodeServiceHeader->addChild('SiteID', (string)$this->getConfigData('id'));
        $nodeServiceHeader->addChild('Password', (string)$this->getConfigData('password'));

        $nodeFrom = $nodeGetQuote->addChild('From');
        $nodeFrom->addChild('CountryCode', $rawRequest->getOrigCountryId());
        $nodeFrom->addChild('Postalcode', $rawRequest->getOrigPostal());
        $nodeFrom->addChild('City', $rawRequest->getOrigCity());

        $nodeBkgDetails = $nodeGetQuote->addChild('BkgDetails');
        $nodeBkgDetails->addChild('PaymentCountryCode', $this->_getPaymentCountryId($rawRequest));
        $nodeBkgDetails->addChild('Date', Varien_Date::now(true));
        $nodeBkgDetails->addChild('ReadyTime', $this->_getReadyTime());
        $nodeBkgDetails->addChild('DimensionUnit', $this->_getDimensionUnit());
        $nodeBkgDetails->addChild('WeightUnit', $this->_getWeightUnit());
        $nodeBkgDetails->addChild('NumberOfPieces', '1');
        $nodeBkgDetails->addChild('ShipmentWeight', $rawRequest->getWeight());

        $nodeTo = $nodeGetQuote->addChild('To');
        $nodeTo->addChild('CountryCode', $rawRequest->getDestCountryId());
        $nodeTo->addChild('Postalcode', $rawRequest->getDestPostal());
        $nodeTo->addChild('City', $rawRequest->getDestCity());

        if ($this->getConfigData('content_type') == self::DOC_NON_DOC) {
            // IsDutiable flag and Dutiable node indicates that cargo is not a documentation
            $nodeBkgDetails->addChild('IsDutiable', 'Y');
            $nodeDutiable = $nodeGetQuote->addChild('Dutiable');
            $nodeDutiable->addChild('DeclaredCurrency', Mage::app()->getStore()->getDefaultCurrencyCode());
            $nodeDutiable->addChild('DeclaredValue', $rawRequest->getValue());
        }

        $request = $xml->asXML();
        $request = utf8_encode($request);
        $responseBody = $this->_getCachedQuotes($request);
        if ($responseBody === null) {
            $debugData = array('request' => $request);
            try {
                $client = new Varien_Http_Client();
                $client->setUri((string)$this->getConfigData('gateway_url'));
                $client->setConfig(array('maxredirects' => 0, 'timeout' => 30));
                $client->setRawData($request);
                $responseBody = $client->request(Varien_Http_Client::POST)->getBody();
                $debugData['result'] = $responseBody;
                $this->_setCachedQuotes($request, $responseBody);
            } catch (Exception $e) {
                $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
                $responseBody = '';
            }
            $this->_debug($debugData);
        }

        return $this->_parseResponse($responseBody);
    }

    /**
     * Parse response from DHL web service
     *
     * @param string $response
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _parseResponse($response)
    {
        $tr = get_html_translation_table(HTML_ENTITIES);
        unset($tr['<'], $tr['>'], $tr['"']);
        $response = str_replace(array_keys($tr), array_values($tr), $response);

        if (strlen(trim($response)) > 0) {
            if (strpos(trim($response), '<?xml') === 0) {
                $xml = simplexml_load_string($response);
                if (is_object($xml)) {
                    if ((isset($xml->Response->Status->ActionStatus) && $xml->Response->Status->ActionStatus == 'Error')
                        || (isset($xml->GetQuoteResponse->Note->Condition))
                    ) {
                        if (isset($xml->Response->Status->Condition)) {
                            $nodeCondition = $xml->Response->Status->Condition;
                        } else {
                            $nodeCondition = $xml->GetQuoteResponse->Note->Condition;
                        }

                        $code = isset($nodeCondition->ConditionCode) ? (string)$nodeCondition->ConditionCode : 0;
                        $data = isset($nodeCondition->ConditionData) ? (string)$nodeCondition->ConditionData : '';
                        $this->_errors[$code] = Mage::helper('usa')->__('Error #%s : %s', $code, $data);
                    } else {
                        if (isset($xml->GetQuoteResponse->BkgDetails->QtdShp)) {
                            foreach ($xml->GetQuoteResponse->BkgDetails->QtdShp as $quotedShipment) {
                                $this->_addRate($quotedShipment);
                            }
                        }
                    }
                }
            } else {
                $this->_errors[] = Mage::helper('usa')->__('The response is in wrong format.');
            }
        }

        /* @var $result Mage_Shipping_Model_Rate_Result */
        $result = Mage::getModel('shipping/rate_result');
        if ($this->_rates) {
            foreach ($this->_rates as $rate) {
                $method = $rate['service'];
                $data = $rate['data'];
                /* @var $rate Mage_Shipping_Model_Rate_Result_Method */
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier(self::CODE);
                $rate->setCarrierTitle($this->getConfigData('title'));
                $rate->setMethod($method);
                $rate->setMethodTitle($data['term']);
                $rate->setCost($data['price_total']);
                $rate->setPrice($data['price_total']);
                $result->append($rate);
            }
        } else if (!empty($this->_errors)) {
            /* @var $error Mage_Shipping_Model_Rate_Result_Error */
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier(self::CODE);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        }
        return $result;
    }

    /**
     * Add rate to DHL rates array
     *
     * @param SimpleXMLElement $nodeQtdShp
     * @return Mage_Usa_Model_Shipping_Carrier_Dhl_International
     */
    protected function _addRate(SimpleXMLElement $nodeQtdShp)
    {
        if (isset($nodeQtdShp->ProductShortName)
            && isset($nodeQtdShp->ShippingCharge)
            && isset($nodeQtdShp->GlobalProductCode)
            && isset($nodeQtdShp->CurrencyCode)
            && array_key_exists((string)$nodeQtdShp->GlobalProductCode, $this->getAllowedMethods())
        ) {
            // DHL product code, e.g. '3', 'A', 'Q', etc.
            $dhlProduct = (string)$nodeQtdShp->GlobalProductCode;
            $totalEstimate = (float)(string)$nodeQtdShp->ShippingCharge;
            $currencyCode = (string)$nodeQtdShp->CurrencyCode;
            $displayCurrencyCode = Mage::app()->getStore()->getDefaultCurrencyCode();
            $dhlProductDescription = $this->getDhlProductDescription($dhlProduct);

            if ($currencyCode != $displayCurrencyCode) {
                // DHL returned shipping estimation in a currency different from store's display currency
                if (isset($nodeQtdShp->ExchangeRate)) {
                    // Convert to USD using DHL reported exchange rate
                    $baseTotalEstimate = $totalEstimate * (float)(string)$nodeQtdShp->ExchangeRate;
                    /* @var $currency Mage_Directory_Model_Currency */
                    $currency = Mage::getModel('directory/currency');
                    $rates = $currency->getCurrencyRates('USD', array($displayCurrencyCode));
                    if (!empty($rates)) {
                        // Convert to store display currency using store exchange rate
                        $totalEstimate = $baseTotalEstimate * $rates[$displayCurrencyCode];
                    } else {
                        $totalEstimate = false;
                        Mage::log("Exchange rate USD->{$displayCurrencyCode} not found. "
                            . "DHL method {$dhlProductDescription} skipped");
                    }
                } else {
                    // Something is wrong
                    $totalEstimate = false;
                }
            }
            if ($totalEstimate) {
                $data = array('term' => $dhlProductDescription,
                    'price_total' => $this->getMethodPrice($totalEstimate, $dhlProduct));
                $this->_rates[] = array('service' => $dhlProduct, 'data' => $data);
            }
        }
        return $this;
    }

    /**
     * Get code of the country which is paying for shipping
     *
     * @param Varien_Object $rawRequest
     * @return string
     */
    protected function _getPaymentCountryId(Varien_Object $rawRequest)
    {
        return $rawRequest->getOrigCountryId();
    }

    /**
     * Return ready time for package (in DHL format)
     * Ready time - time between order submission and package ready
     *
     * @return string
     */
    protected function _getReadyTime()
    {
        $readyTime = (int)(string)$this->getConfigData('ready_time');
        return "PT{$readyTime}H00M";
    }

    /**
     * Returns dimension unit (inch or cm)
     *
     * @return string
     */
    protected function _getDimensionUnit()
    {
        $countryId = $this->_rawRequest->getOrigCountryId();
        $measureUnit = $this->getCountryParams($countryId)->getMeasureUnit();
        if (empty($measureUnit)) {
            Mage::throwException(Mage::helper('usa')->__("Can not identify measure unit for %s", $countryId));
        }
        return $measureUnit;
    }

    /**
     * Returns weight unit (Kg or pound)
     *
     * @return string
     */
    protected function _getWeightUnit()
    {
        $countryId = $this->_rawRequest->getOrigCountryId();
        $weightUnit = $this->getCountryParams($countryId)->getWeightUnit();
        if (empty($weightUnit)) {
            Mage::throwException(Mage::helper('usa')->__("Can not identify weight unit for %s", $countryId));
        }
        return $weightUnit;
    }

    /**
     * Get Country Params by Country Code
     *
     * @param string $countryCode
     * @return Varien_Object
     *
     * @see $countryCode ISO 3166 Codes (Countries) A2
     */
    protected function getCountryParams($countryCode)
    {
        if (empty($this->_countryParams)) {
            $dhlConfigPath = Mage::getModuleDir('etc', 'Mage_Usa')  . DS . 'dhl' . DS;
            $countriesXml = file_get_contents($dhlConfigPath . 'international' . DS . 'countries.xml');
            $this->_countryParams = new Varien_Simplexml_Element($countriesXml);
        }
        if (isset($this->_countryParams->$countryCode)) {
            $return = new Varien_Object($this->_countryParams->$countryCode->asArray());
        }
        return isset($return) ? $return : new Varien_Object();
    }

    /**
     * Do shipment request to carrier web service, obtain Print Shipping Labels and process errors in response
     *
     * @param Varien_Object $request
     * @return Varien_Object
     */
    protected function _doShipmentRequest(Varien_Object $request)
    {
        $this->_prepareShipmentRequest($request);
        $request->setAction('GenerateLabel');
        $this->_mapRequestToShipment($request);
        $this->setRequest($request);

        return $this->_doRequest();
    }
}
