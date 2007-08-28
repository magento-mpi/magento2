<?php

/**
 * USPS shipping rates estimation
 *
 * @link       http://www.usps.com/webtools/htm/Development-Guide.htm
 * @package    Mage
 * @subpackage Mage_Usa
 * @author     Sergiy Lysak <sergey@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Usa_Model_Shipping_Carrier_Usps extends Mage_Shipping_Model_Carrier_Abstract
{
    protected $_request = null;
    protected $_result = null;
    protected $_defaultGatewayUrl = 'http://production.shippingapis.com/ShippingAPI.dll';
    #protected $_defaultGatewayUrl = 'https://secure.shippingaps.com/ShippingAPI.dll';

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!Mage::getStoreConfig('carriers/usps/active')) {
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

        if ($request->getLimitMethod()) {
            $r->setService($request->getLimitMethod());
        } else {
            $r->setService('ALL');
        }

        if ($request->getUspsUserid()) {
            $userId = $request->getUspsUserid();
        } else {
            $userId = Mage::getStoreConfig('carriers/usps/userid');
        }
        $r->setUserId($userId);

        if ($request->getUspsContainer()) {
            $container = $request->getUspsContainer();
        } else {
            $container = Mage::getStoreConfig('carriers/usps/container');
        }
        $r->setContainer($container);

        if ($request->getUspsSize()) {
            $size = $request->getUspsSize();
        } else {
            $size = Mage::getStoreConfig('carriers/usps/size');
        }
        $r->setSize($size);

        if ($request->getUspsMachinable()) {
            $machinable = $request->getUspsMachinable();
        } else {
            $machinable = Mage::getStoreConfig('carriers/usps/machinable');
        }
        $r->setMachinable($machinable);

        if ($request->getOrigPostcode()) {
            $r->setOrigPostal($request->getOrigPostcode());
        } else {
            $r->setOrigPostal(Mage::getStoreConfig('shipping/origin/postcode'));
        }

        if ($request->getDestCountryId()) {
            $destCountry = $request->getDestCountryId();
        } else {
            $destCountry = 223;
        }
        $r->setDestCountryId($destCountry);

        $countries = Mage::getResourceModel('directory/country_collection')
                        ->addCountryIdFilter($destCountry)
                        ->load()
                        ->getItems();
        $country = array_shift($countries);
        $r->setDestCountryName($country->getName());

        if ($request->getDestPostcode()) {
            $r->setDestPostal($request->getDestPostcode());
        } else {
            $r->setDestPostal('90034');
        }

        $r->setWeightPounds(floor($request->getPackageWeight()));
        $r->setWeightOunces(($request->getPackageWeight()-floor($request->getPackageWeight()))*16);

        $r->setValue($request->getPackageValue());

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

        if ($r->getDestCountryId() == 223) {
            $xml = new SimpleXMLElement('<RateV3Request/>');

            $xml->addAttribute('USERID', $r->getUserId());

            $package = $xml->addChild('Package');
                $package->addAttribute('ID', 0);
                $package->addChild('Service', $r->getService());
    //          $package->addChild('FirstClassMailType', $r->getService());
                $package->addChild('ZipOrigination', $r->getOrigPostal());
                $package->addChild('ZipDestination', $r->getDestPostal());
                $package->addChild('Pounds', $r->getWeightPounds());
                $package->addChild('Ounces', $r->getWeightOunces());
                $package->addChild('Container', $r->getContainer());
                $package->addChild('Size', $r->getSize());
                $package->addChild('Machinable', $r->getMachinable());

            $request = $xml->asXML();

            try {
                $url = Mage::getStoreConfig('carriers/usps/gateway_url');
                if (!$url) {
                    $url = $this->_defaultGatewayUrl;
                }
                $client = new Zend_Http_Client();
                $client->setUri($url);
                $client->setConfig(array('maxredirects'=>0, 'timeout'=>30));
                $client->setParameterGet('API', 'RateV3');
                $client->setParameterGet('XML', $request);
                $response = $client->request();
                $responseBody = $response->getBody();
            } catch (Exception $e) {
                $responseBody = '';
            }
        } else {
            $xml = new SimpleXMLElement('<IntlRateRequest/>');

            $xml->addAttribute('USERID', $r->getUserId());

            $package = $xml->addChild('Package');
                $package->addAttribute('ID', 0);
                $package->addChild('Pounds', $r->getWeightPounds());
                $package->addChild('Ounces', $r->getWeightOunces());
                $package->addChild('MailType', 'Package');
                $package->addChild('ValueOfContents', $r->getValue());
                $package->addChild('Country', $r->getDestCountryName());

            $request = $xml->asXML();

            try {
                $client = new Zend_Http_Client();
                $client->setUri(Mage::getStoreConfig('carriers/usps/gateway_url'));
                $client->setConfig(array('maxredirects'=>0, 'timeout'=>30));
                $client->setParameterGet('API', 'IntlRate');
                $client->setParameterGet('XML', $request);
                $response = $client->request();
                $responseBody = $response->getBody();
            } catch (Exception $e) {
                $responseBody = '';
            }
        }

        $this->_parseXmlResponse($responseBody);
    }

    protected function _parseXmlResponse($response)
    {
        $costArr = array();
        $priceArr = array();
        $errorTitle = 'Unable to retrieve quotes';
        if (strlen(trim($response))>0) {
            if (strpos(trim($response), '<?xml')===0) {
                $xml = simplexml_load_string($response);
                    if (is_object($xml)) {
                        if (is_object($xml->Number) && is_object($xml->Description) && (string)$xml->Description!='') {
                            $errorTitle = (string)$xml->Description;
                        } elseif (is_object($xml->Package) && is_object($xml->Package->Error) && is_object($xml->Package->Error->Description) && (string)$xml->Package->Error->Description!='') {
                            $errorTitle = (string)$xml->Package->Error->Description;
                        } else {
                            $errorTitle = 'Unknown error';
                        }
                        $r = $this->_rawRequest;
                        $allowedMethods = explode(",", Mage::getStoreConfig('carriers/usps/allowed_methods'));
                        if ($r->getDestCountryId() == 223) {
                            if (is_object($xml->Package) && is_object($xml->Package->Postage)) {
                                foreach ($xml->Package->Postage as $postage) {
                                    if (in_array($this->getCode('service_to_code', (string)$postage->MailService), $allowedMethods)) {
                                        $costArr[(string)$postage->MailService] = (string)$postage->Rate;
                                        $priceArr[(string)$postage->MailService] = $this->getMethodPrice((string)$postage->Rate, $this->getCode('service_to_code', (string)$postage->MailService));
                                    }
                                }
                                asort($priceArr);
                            }
                        } else {
                            if (is_object($xml->Package) && is_object($xml->Package->Service)) {
                                foreach ($xml->Package->Service as $service) {
                                    if (in_array($this->getCode('service_to_code', (string)$service->SvcDescription), $allowedMethods)) {
                                        $costArr[(string)$service->SvcDescription] = (string)$service->Postage;
                                        $priceArr[(string)$service->SvcDescription] = $this->getMethodPrice((string)$service->Postage, $this->getCode('service_to_code', (string)$service->SvcDescription));
                                    }
                                }
                                asort($priceArr);
                            }
                        }
                    }
            } else {
                $errorTitle = 'Response is in the wrong format';
            }
        }

        $result = Mage::getModel('shipping/rate_result');
        $defaults = $this->getDefaults();
        if (empty($priceArr)) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('usps');
            $error->setCarrierTitle(Mage::getStoreConfig('carriers/usps/title'));
            $error->setErrorMessage($errorTitle);
            $result->append($error);
        } else {
            foreach ($priceArr as $method=>$price) {
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier('usps');
                $rate->setCarrierTitle(Mage::getStoreConfig('carriers/usps/title'));
                $rate->setMethod($method);
                $rate->setMethodTitle($method);
                $rate->setCost($costArr[$method]);
                $rate->setPrice($price);
                $result->append($rate);
            }
        }
        $this->_result = $result;
    }

    public function getMethodPrice($cost, $method='')
    {
        $r = $this->_rawRequest;
        if (Mage::getStoreConfig('carriers/usps/cutoff_cost') != ''
         && $method == Mage::getStoreConfig('carriers/usps/free_method')
         && Mage::getStoreConfig('carriers/usps/cutoff_cost') <= $r->getValue()) {
             $price = '0.00';
        } else {
            $price = $cost + Mage::getStoreConfig('carriers/usps/handling');
        }
        return $price;
    }

    public function getCode($type, $code='')
    {
        static $codes = array(

            'service'=>array(
                'FIRST CLASS' => 'First-Class',
                'PRIORITY'    => 'Priority Mail',
                'EXPRESS'     => 'Express Mail',
                'BPM'         => 'Bound Printed Matter',
                'PARCEL'      => 'Parcel Post',
                'MEDIA'       => 'Media Mail',
                'LIBRARY'     => 'Library',
//                'ALL'         => 'All Services',
            ),

            'service_to_code'=>array(
                'First-Class'                      => 'FIRST CLASS',
                'Express Mail'                     => 'EXPRESS',
                'Express Mail PO to PO'            => 'EXPRESS',
                'Priority Mail'                    => 'PRIORITY',
                'Parcel Post'                      => 'PARCEL',
                'Express Mail Flat-Rate Envelope'  => 'EXPRESS',
                'Priority Mail Flat-Rate Box'      => 'PRIORITY',
                'Bound Printed Matter'             => 'BPM',
                'Media Mail'                       => 'MEDIA',
                'Library Mail'                     => 'LIBRARY',
                'Priority Mail Flat-Rate Envelope' => 'PRIORITY',
                'Global Express Guaranteed'        => 'EXPRESS',
                'Global Express Guaranteed Non-Document Rectangular'     => 'EXPRESS',
                'Global Express Guaranteed Non-Document Non-Rectangular' => 'EXPRESS',
                'Express Mail International (EMS)'                       => 'EXPRESS',
                'Express Mail International (EMS) Flat Rate Envelope'    => 'EXPRESS',
                'Priority Mail International'                            => 'PRIORITY',
                'Priority Mail International Flat Rate Box'              => 'PRIORITY',
            ),

            'first_class_mail_type'=>array(
                'LETTER'      => 'Letter',
                'FLAT'        => 'Flat',
                'PARCEL'      => 'Parcel',
            ),

            'container'=>array(
                'VARIABLE'           => 'Variable',
                'FLAT RATE BOX'      => 'Flat Rate Box',
                'FLAT RATE ENVELOPE' => 'Flat Rate Envelope',
                'RECTANGULAR'        => 'Rectangular',
                'NONRECTANGULAR'     => 'Non-rectangular',
            ),

            'size'=>array(
                'REGULAR'     => 'Regular',
                'LARGE'       => 'Large',
                'OVERSIZE'    => 'Oversize',
            ),

            'machinable'=>array(
                'true'        => 'Yes',
                'false'       => 'No',
            ),

        );

        if (!isset($codes[$type])) {
//            throw Mage::exception('Mage_Shipping', 'Invalid USPS XML code type: '.$type);
        } elseif (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
//            throw Mage::exception('Mage_Shipping', 'Invalid USPS XML code for type '.$type.': '.$code);
        } else {
            return $codes[$type][$code];
        }
    }

}