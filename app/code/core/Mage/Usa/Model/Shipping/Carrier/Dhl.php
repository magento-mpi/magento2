<?php

/**
 * DHL shipping rates estimation
 *
 * @package    Mage
 * @subpackage Mage_Usa
 * @author     Sergiy Lysak <sergey@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Usa_Model_Shipping_Carrier_Dhl extends Mage_Shipping_Model_Carrier_Abstract
{
    protected $_request = null;
    protected $_result = null;

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!Mage::getStoreConfig('carriers/dhl/active')) {
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
        }

        if ($request->getDhlId()) {
            $id = $request->getDhlId();
        } else {
            $id = Mage::getStoreConfig('carriers/dhl/id');
        }
        $r->setId($id);

        if ($request->getDhlPassword()) {
            $password = $request->getDhlPassword();
        } else {
            $password = Mage::getStoreConfig('carriers/dhl/password');
        }
        $r->setPassword($password);

        if ($request->getDhlAccount()) {
            $accountNbr = $request->getDhlAccount();
        } else {
            $accountNbr = Mage::getStoreConfig('carriers/dhl/account');
        }
        $r->setAccountNbr($accountNbr);

        if ($request->getDhlShippingKey()) {
            // dorabotat' v plane zaprosa shipping key pri ego otsutstvii
            // ili pri izmenenii ZIP code magazina! TOFIX, FIXME
            $shippingKey = $request->getDhlShippingKey();
        } else {
            $shippingKey = Mage::getStoreConfig('carriers/dhl/shipping_key');
        }
        $r->setShippingKey($shippingKey);

        if ($request->getDhlShipmentType()) {
            $shipmentType = $request->getDhlShipmentType();
        } else {
            $shipmentType = Mage::getStoreConfig('carriers/dhl/shipment_type');
        }
        $r->setShipmentType($shipmentType);

        if ($request->getDestPostcode()) {
            $r->setDestPostal($request->getDestPostcode());
        } else {
            $r->setDestPostal('90034');
        }

        $r->setWeight($request->getPackageWeight());
        
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

        $xml = new SimpleXMLElement('<eCommerce/>');
        $xml->addAttribute('action', 'Request');
        $xml->addAttribute('version', '1.1');

        $requestor = $xml->addChild('Requestor');
            $requestor->addChild('ID', $r->getId());
            $requestor->addChild('Password', $r->getPassword());

        $shipment = $xml->addChild('Shipment');
            $shipment->addAttribute('action', 'RateEstimate');
            $shipment->addAttribute('version', '1.0');

            $shippingCredentials = $shipment->addChild('ShippingCredentials');
                $shippingCredentials->addChild('ShippingKey', $r->getShippingKey());
                $shippingCredentials->addChild('AccountNbr', $r->getAccountNbr());

            $shipmentDetail = $shipment->addChild('ShipmentDetail');
                $shipmentDetail->addChild('ShipDate', date('Y-m-d'));
                if ($r->hasService()) {
                    $shipmentDetail->addChild('Service')->addChild('Code', $r->getService());
                }
                $shipmentDetail->addChild('ShipmentType')->addChild('Code', $r->getShipmentType());
                $shipmentDetail->addChild('Weight', $r->getWeight());

            $shipment->addChild('Billing')->addChild('Party')->addChild('Code', 'S'); // Sender
            
            $receiverAddress = $shipment->addChild('Receiver')->addChild('Address');
//              $receiverAddress->addChild('State', $r->getDestState());
                $receiverAddress->addChild('Country', 'US');
                $receiverAddress->addChild('PostalCode', $r->getDestPostal());

        $request = $xml->asXML();

/*
        $client = new Zend_Http_Client();
        $client->setUri(Mage::getStoreConfig('carriers/dhl/gateway_url'));
        $client->setConfig(array('maxredirects'=>0, 'timeout'=>30));
        $client->setParameterPost($request);
        $response = $client->request();
        $responseBody = $response->getBody();
*/

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, Mage::getStoreConfig('carriers/dhl/gateway_url'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        $responseBody = curl_exec($ch);
        curl_close ($ch);

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
                if (
                       is_object($xml->Faults)
                    && is_object($xml->Faults->Fault)
                    && is_object($xml->Faults->Fault->Code)
                    && is_object($xml->Faults->Fault->Description)
                    && is_object($xml->Faults->Fault->Context)
                   ) {
                    $errorTitle = 'Error #'.(string)$xml->Faults->Fault->Code.': '.$xml->Faults->Fault->Description.' ('.$xml->Faults->Fault->Context.')';
                } else {
                    $errorTitle = 'Unknown error';
                }
                /*
                FIXME, TOFIX

                REWORK IT: nado perepisat' etot kusok dlia polucheniya pravil'nyh
                ocenok stoimosti iz pravil'nyh poley:
                
                if (is_object($xml->Package) && is_object($xml->Package->Postage)) {
                    foreach ($xml->Package->Postage as $postage) {
                        $rArr[(string)$postage->MailService] = (string)$postage->Rate;
                    }
                    arsort($rArr);
                }
                */
            }
        } else {
            $errorTitle = 'Response is in the wrong format';
        }

        $result = Mage::getModel('shipping/rate_result');
        $defaults = $this->getDefaults();
        if (empty($rArr)) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('dhl');
            $error->setCarrierTitle(Mage::getStoreConfig('carriers/dhl/title'));
            $error->setErrorMessage($errorTitle);
            $result->append($error);
        } else {
            foreach ($rArr as $method=>$cost) {
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier('dhl');
                $rate->setCarrierTitle(Mage::getStoreConfig('carriers/dhl/title'));
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
        $price = $cost + Mage::getStoreConfig('carriers/dhl/handling');
        return $price;
    }

    public function getCode($type, $code='')
    {
        static $codes = array(

            'service'=>array(
                'E' => 'Express',
                'N' => 'Next Afternoon',
                'S' => 'Second Day Service',
                'G' => 'Ground',
            ),

            'shipment_type'=>array(
                'L' => 'Letter',
                'P' => 'Package',
            ),

        );

        if (!isset($codes[$type])) {
            throw Mage::exception('Mage_Shipping', 'Invalid DHL XML code type: '.$type);
        }
        
        if (''===$code) {
            return $codes[$type];
        }
        
        if (!isset($codes[$type][$code])) {
            throw Mage::exception('Mage_Shipping', 'Invalid DHL XML code for type '.$type.': '.$code);
        }
        
        return $codes[$type][$code];
    }

}