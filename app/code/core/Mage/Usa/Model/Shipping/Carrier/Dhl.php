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
 * @package    Mage_Usa
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * DHL shipping rates estimation
 *
 * @category   Mage
 * @package    Mage_Usa
 * @author     Sergiy Lysak <sergey@varien.com>
 */
class Mage_Usa_Model_Shipping_Carrier_Dhl extends Mage_Shipping_Model_Carrier_Abstract
{
    protected $_request = null;
    protected $_result = null;
    protected $_defaultGatewayUrl = 'https://eCommerce.airborne.com/ApiLandingTest.asp';

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

        try {
            $url = Mage::getStoreConfig('carriers/dhl/gateway_url');
            if (!$url) {
                $url = $this->_defaultGatewayUrl;
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
            $responseBody = curl_exec($ch);
            curl_close ($ch);
        } catch (Exception $e) {
            $responseBody = '';
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
                        $allowedMethods = explode(",", Mage::getStoreConfig('carriers/dhl/allowed_methods'));
                        foreach ($xml->Package->Postage as $postage) {
                            $costArr[(string)$postage->MailService] = (string)$postage->Rate;
                            $priceArr[(string)$postage->MailService] = $this->getMethodPrice((string)$postage->Rate, (string)$postage->MailService);
                        }
                        asort($priceArr);
                    }
                    */
                }
            } else {
                $errorTitle = 'Response is in the wrong format';
            }
        }

        $result = Mage::getModel('shipping/rate_result');
        $defaults = $this->getDefaults();
        if (empty($priceArr)) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('dhl');
            $error->setCarrierTitle(Mage::getStoreConfig('carriers/dhl/title'));
            $error->setErrorMessage($errorTitle);
            $result->append($error);
        } else {
            foreach ($priceArr as $method=>$price) {
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier('dhl');
                $rate->setCarrierTitle(Mage::getStoreConfig('carriers/dhl/title'));
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
        if (Mage::getStoreConfig('carriers/dhl/cutoff_cost') != ''
         && $method == Mage::getStoreConfig('carriers/dhl/free_method')
         && Mage::getStoreConfig('carriers/dhl/cutoff_cost') <= $r->getValue()) {
             $price = '0.00';
        } else {
            $price = $cost + Mage::getStoreConfig('carriers/dhl/handling');
        }
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
//            throw Mage::exception('Mage_Shipping', 'Invalid DHL XML code type: '.$type);
        } elseif (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
//            throw Mage::exception('Mage_Shipping', 'Invalid DHL XML code for type '.$type.': '.$code);
        } else {
            return $codes[$type][$code];
        }
    }

}