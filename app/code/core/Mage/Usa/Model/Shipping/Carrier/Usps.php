<?php

/**
 * USPS shipping rates estimation
 *
 * @package    Mage
 * @subpackage Mage_Usa
 * @author     Sergiy Lysak <sergey@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Usa_Model_Shipping_Carrier_Usps extends Mage_Shipping_Model_Carrier_Abstract
{
    protected $_request = null;
    protected $_result = null;

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
        
        if ($request->getDestPostcode()) {
            $r->setDestPostal($request->getDestPostcode());
        } else {
            $r->setDestPostal('90034');
        }

        $r->setWeightPounds(floor($request->getPackageWeight()));
        $r->setWeightOunces(($request->getPackageWeight()-floor($request->getPackageWeight()))*16);
        
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

        $client = new Zend_Http_Client();
        $client->setUri(Mage::getStoreConfig('carriers/usps/gateway_url'));
        $client->setConfig(array('maxredirects'=>0, 'timeout'=>30));
        $client->setParameterGet('API', 'RateV3');
        $client->setParameterGet('XML', $request);
        $response = $client->request();
        $responseBody = $response->getBody();

        $this->_parseXmlResponse($responseBody);
    }
    
    protected function _parseXmlResponse($response)
    {
        $rArr = array();
        $errorTitle = 'Unable to retrieve quotes';
        if (strpos(trim($response), '<?xml')===0)
        {
            $xml = simplexml_load_string($response);
            if (is_object($xml)) {
                if (is_object($xml->Number) && is_object($xml->Description) && (string)$xml->Description!='') {
                    $errorTitle = (string)$xml->Description;
                } elseif (is_object($xml->Package) && is_object($xml->Package->Error) && is_object($xml->Package->Error->Description) && (string)$xml->Package->Error->Description!='') {
                    $errorTitle = (string)$xml->Package->Error->Description;
                } else {
                    $errorTitle = 'Unknown error';
                }
                if (is_object($xml->Package) && is_object($xml->Package->Postage)) {
                    foreach ($xml->Package->Postage as $postage) {
                        $rArr[(string)$postage->MailService] = (string)$postage->Rate;
                    }
                    arsort($rArr);
                }
            }
        } else {
            $errorTitle = 'Response is in the wrong format';
        }

        $result = Mage::getModel('shipping/rate_result');
        $defaults = $this->getDefaults();
        if (empty($rArr)) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('usps');
            $error->setCarrierTitle(Mage::getStoreConfig('carriers/usps/title'));
            $error->setErrorMessage($errorTitle);
            $result->append($error);
        } else {
            foreach ($rArr as $method=>$cost) {
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier('usps');
                $rate->setCarrierTitle(Mage::getStoreConfig('carriers/usps/title'));
                $rate->setMethod($method);
                $rate->setMethodTitle($method);
                $rate->setCost($cost);
                $rate->setPrice($this->getMethodPrice($cost));
                $result->append($rate);
            }
        }
        $this->_result = $result;
    }
    
    public function getMethodPrice($cost)
    {
        $price = $cost+Mage::getStoreConfig('carriers/usps/handling');
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
            throw Mage::exception('Mage_Shipping', 'Invalid USPS XML code type: '.$type);
        }
        
        if (''===$code) {
            return $codes[$type];
        }
        
        if (!isset($codes[$type][$code])) {
            throw Mage::exception('Mage_Shipping', 'Invalid USPS XML code for type '.$type.': '.$code);
        }
        
        return $codes[$type][$code];
    }

}