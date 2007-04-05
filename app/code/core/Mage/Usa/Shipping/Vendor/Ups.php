<?php

class Mage_Usa_Shipping_Vendor_Ups extends Mage_Sales_Shipping_Vendor_Abstract
{
    protected $_request = null;
    protected $_result = null;

    protected $_defaults = null;
    protected $_data = array();
    
    public function getDefaults()
    {
        if (empty($this->_defaults)) {
            $this->_defaults = Mage::getConfig('Mage_Sales')->getShippingDefaults($this->_data['vendor']);
        }
        return $this->_defaults;    
    }
    
    public function fetchQuotes(Mage_Sales_Shipping_Quote_Request $request, $requestType='cgi')
    {
        $this->setRequest($request);

        switch ($requestType) {
            case 'cgi':
                $this->_getCgiQuotes();
                break;
            
            case 'xml':
                $this->_getXmlQuotes();
                break;
        }
        
        return $this->getResult();
    }
    
    protected function setRequest(Mage_Sales_Shipping_Quote_Request $request)
    {
        $this->_request = $request;

        $this->_data['vendor'] = $request->getVendor();

        $defaults = $this->getDefaults();

        if ($request->getLimitService()) {
            $this->_data['action'] = $this->getCode('action', 'single');
            $this->_data['product'] = $request->getLimitService();
        } else {
            $this->_data['action'] = $this->getCode('action', 'all');
            $this->_data['product'] = 'GNDRES';
        }
        
        if ($request->getUpsPickup()) {
            $this->_data['pickup'] = $this->getCode('pickup', $request->getUpsPickup());
        } else {
            $this->_data['pickup'] = $this->getCode('pickup', (string)$defaults->pickup);
        }
        
        if ($request->getUpsContainer()) {
            $this->_data['container'] = $this->getCode('container', $request->getUpsContainer());
        } else {
            $this->_data['container'] = $this->getCode('container', (string)$defaults->container);
        }
        
        if ($request->getUpsDestType()) {
            $this->_data['destType'] = $this->getCode('destType', $request->getUpsDestType());
        } else {
            $this->_data['destType'] = $this->getCode('destType', (string)$defaults->destType);
        }
                
        $this->_data['origCountry'] = 'US';#Mage::registry('directory')->getCountryById($request->getOrigCountry(), 'iso_code_2');
        $this->_data['origPostal'] = $request->getOrigPostcode();
        
        $this->_data['destCountry'] = 'US';#Mage::registry('directory')->getCountryById($request->getDestCountry(), 'iso_code_2');
        $this->_data['destPostal'] = $request->getDestPostcode();
        
        $this->_data['weight'] = $request->getPackageWeight();
    }
    
    public function getResult()
    {
       return $this->_result;
    }

    protected function _getCgiQuotes()
    {
        $r = $this->_data;
        
        $cgi = Mage::getConfig('Mage_Sales')->getShippingDefaults($r['vendor'])->cgi;
        
        $params = array(
            'accept_UPS_license_agreement' => 'yes',
            '10_action'      => $r['action'],
            '13_product'     => $r['product'],
            '14_origCountry' => $r['origCountry'],
            '15_origPostal'  => $r['origPostal'],
            '19_destPostal'  => $r['destPostal'],
            '22_destCountry' => $r['destCountry'],
            '23_weight'      => $r['weight'],
            '47_rate_chart'  => $r['pickup'],
            '48_container'   => $r['container'],
            '49_residential' => $r['destType'],
        );

        $client = new Zend_Http_Client();
        $uri = ((string)$cgi->proto).'://'.((string)$cgi->host).':'.((string)$cgi->port).((string)$cgi->url);
        $client->setUri($uri);
        $client->setParameterGet($params);
        $response = $client->request();
        $responseBody = $response->getBody();

        $this->_parseCgiResponse($responseBody);
    }
    
    protected function _parseCgiResponse($response)
    {
        $rRows = explode("\n", $response);
        $rArr = array();
        $errorTitle = 'Unknown error';
        foreach ($rRows as $rRow) {
            $r = explode('%', $rRow);
            switch (substr($r[0],-1)) {
                case 3: case 4:
                    $rArr[] = array('service'=>$r[1], 'cost'=>$r[8]);
                    break;
                case 5:
                    $errorTitle = $r[1];
                    break;
                case 6:
                    $rArr[] = array('service'=>$r[3], 'cost'=>$r[10]);
                    break;
            }
        }
        
        $result = new Mage_Sales_Shipping_Quote_Result();
        if (empty($rArr)) {
            $error = new Mage_Sales_Shipping_Quote_Service_Error();
            $error->setTitle($errorTitle);
            $result->append($error);
        } else {
            $defaults = $this->getDefaults();
            
            foreach ($rArr as $r) {
                $quote = new Mage_Sales_Shipping_Quote_Service();
                $quote->setVendor($this->_data['vendor']);
                $quote->setVendorTitle((string)$defaults->title);
                $quote->setService($r['service']);
                $quote->setServiceTitle($this->getCode('service', $r['service']));
                $quote->setCost($r['cost']);
                $quote->setPrice($this->getServicePrice($r));
                $result->append($quote);
            }
        }

        $this->_result = $result;
    }
    
    public function getServicePrice($r)
    {
        $defaults = $this->getDefaults();
        $price = $r['cost']+(float)$defaults->handling;
        return $price;
    }

    public function getCode($type, $code='')
    {
        static $codes = array(
            'action'=>array(
                'single'=>'3',
                'all'=>'4',
            ),
            
            'service'=>array(
                '1DM'    => 'Next Day Air Early AM',
                '1DML'   => 'Next Day Air Early AM Letter',
                '1DA'    => 'Next Day Air',
                '1DAL'   => 'Next Day Air Letter',
                '1DAPI'  => 'Next Day Air Intra (Puerto Rico)',
                '1DP'    => 'Next Day Air Saver',
                '1DPL'   => 'Next Day Air Saver Letter',
                '2DM'    => '2nd Day Air AM',
                '2DML'   => '2nd Day Air AM Letter',
                '2DA'    => '2nd Day Air',
                '2DAL'   => '2nd Day Air Letter',
                '3DS'    => '3 Day Select',
                'GND'    => 'Ground',
                'GNDCOM' => 'Ground Commercial',
                'GNDRES' => 'Ground Residential',
                'STD'    => 'Canada Standard',
                'XPR'    => 'Worldwide Express',
                'XPRL'   => 'worldwide Express Letter',
                'XDM'    => 'Worldwide Express Plus',
                'XDML'   => 'Worldwide Express Plus Letter',
                'XPD'    => 'Worldwide Expedited',
            ),
            
            'pickup'=>array(
                'RDP'    => 'Regular Daily Pickup',
                'OCA'    => 'On Call Air',
                'OTP'    => 'One Time Pickup',
                'LC'     => 'Letter Center',
                'CC'     => 'Customer Counter',
            ),
            
            'container'=>array(
                'CP'     => '00', // Customer Packaging
                'ULE'    => '01', // UPS Letter Envelope
                'UT'     => '03', // UPS Tube
                'UEB'    => '21', // UPS Express Box
                'UW25'   =>' 24', // UPS Worldwide 25 kilo
                'UW10'   => '25', //UPS Worldwide 10 kilo
            ),
            
            'destType'=>array(
                'RES'    => '1', // Residential
                'COM'    => '2', // Commercial
            ),
        );
        
        if (!isset($codes[$type])) {
            Mage::exception('Invalid UPS CGI code type: '.$type);
        }
        
        if (''===$code) {
            return $codes[$type];
        }
        
        if (!isset($codes[$type][$code])) {
            Mage::exception('Invalid UPS CGI code for type '.$type.': '.$code);
        }
        
        return $codes[$type][$code];
    }

    protected function _getXmlQuotes()
    {
        
    }

}