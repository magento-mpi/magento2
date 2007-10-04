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
 * UPS shipping rates estimation
 *
 * @category   Mage
 * @package    Mage_Usa
 * @author     Moshe Gurvich <moshe@varien.com>
 * @author     Sergiy Lysak <sergey@varien.com>
 */
class Mage_Usa_Model_Shipping_Carrier_Ups extends Mage_Usa_Model_Shipping_Carrier_Abstract
{
    protected $_request = null;
    protected $_result = null;
    protected $_defaultCgiGatewayUrl = 'http://www.ups.com:80/using/services/rave/qcostcgi.cgi';

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!Mage::getStoreConfig('carriers/ups/active')) {
            return false;
        }

        $this->setRequest($request);
        if (!$request->getUpsRequestMethod()) {
            $request->setUpsRequestMethod('cgi');
        }

        switch ($request->getUpsRequestMethod()) {
            case 'cgi':
                $this->_getCgiQuotes();
                break;

            case 'xml':
                $this->_getXmlQuotes();
                break;
        }

        return $this->getResult();
    }

    public function setRequest(Mage_Shipping_Model_Rate_Request $request)
    {
        $this->_request = $request;

        $r = new Varien_Object();

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

        if ($request->getDestCountryId()) {
            $destCountry = $request->getDestCountryId();
        } else {
            $destCountry = self::USA_COUNTRY_ID;
        }
        $r->setDestCountry(Mage::getModel('directory/country')->load($destCountry)->getIso2Code());

        if ($request->getDestPostcode()) {
            $r->setDestPostal($request->getDestPostcode());
        } else {

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

    protected function _getCgiQuotes()
    {
        $r = $this->_rawRequest;

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

        try {
            $url = Mage::getStoreConfig('carriers/ups/gateway_url');
            if (!$url) {
                $url = $this->_defaultCgiGatewayUrl;
            }
            $client = new Zend_Http_Client();
            $client->setUri($url);
            $client->setConfig(array('maxredirects'=>0, 'timeout'=>30));
            $client->setParameterGet($params);
            $response = $client->request();
            $responseBody = $response->getBody();
        } catch (Exception $e) {
            $responseBody = '';
        }

        $this->_parseCgiResponse($responseBody);
    }

    protected function _parseCgiResponse($response)
    {
        $rRows = explode("\n", $response);
        $costArr = array();
        $priceArr = array();
        $errorTitle = 'Unknown error';
        $allowedMethods = explode(",", Mage::getStoreConfig('carriers/ups/allowed_methods'));
        foreach ($rRows as $rRow) {
            $r = explode('%', $rRow);
            switch (substr($r[0],-1)) {
                case 3: case 4:
                    if (in_array($r[1], $allowedMethods)) {
                        $costArr[$r[1]] = $r[8];
                        $priceArr[$r[1]] = $this->getMethodPrice($r[8], $r[1]);
                    }
                    break;
                case 5:
                    $errorTitle = $r[1];
                    break;
                case 6:
                    if (in_array($r[3], $allowedMethods)) {
                        $costArr[$r[3]] = $r[10];
                        $priceArr[$r[3]] = $this->getMethodPrice($r[10], $r[3]);
                    }
                    break;
            }
        }
        asort($priceArr);

        $result = Mage::getModel('shipping/rate_result');
        $defaults = $this->getDefaults();
        if (empty($priceArr)) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('ups');
            $error->setCarrierTitle(Mage::getStoreConfig('carriers/ups/title'));
            $error->setErrorMessage($errorTitle);
            $result->append($error);
        } else {
            foreach ($priceArr as $method=>$price) {
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier('ups');
                $rate->setCarrierTitle(Mage::getStoreConfig('carriers/ups/title'));
                $rate->setMethod($method);
                $rate->setMethodTitle($this->getCode('method', $method));
                $rate->setCost($costArr[$method]);
                $rate->setPrice($price);
                $result->append($rate);
            }
        }
#echo "<pre>".print_r($result,1)."</pre>";
        $this->_result = $result;
    }

    public function getMethodPrice($cost, $method='')
    {
        $r = $this->_rawRequest;
        if (Mage::getStoreConfig('carriers/ups/cutoff_cost') != ''
         && $method == Mage::getStoreConfig('carriers/ups/free_method')
         && Mage::getStoreConfig('carriers/ups/cutoff_cost') <= $r->getValue()) {
             $price = '0.00';
        } else {
            $price = $cost + Mage::getStoreConfig('carriers/ups/handling');
        }
        return $price;
    }

/*
    public function isEligibleForFree($method)
    {
    	return $method=='GND' || $method=='GNDCOM' || $method=='GNDRES';
    }
*/

    public function getCode($type, $code='')
    {
        $codes = array(
            'action'=>array(
                'single'=>'3',
                'all'=>'4',
            ),

            'method'=>array(
                '1DM'    => __('Next Day Air Early AM'),
                '1DML'   => __('Next Day Air Early AM Letter'),
                '1DA'    => __('Next Day Air'),
                '1DAL'   => __('Next Day Air Letter'),
                '1DAPI'  => __('Next Day Air Intra (Puerto Rico)'),
                '1DP'    => __('Next Day Air Saver'),
                '1DPL'   => __('Next Day Air Saver Letter'),
                '2DM'    => __('2nd Day Air AM'),
                '2DML'   => __('2nd Day Air AM Letter'),
                '2DA'    => __('2nd Day Air'),
                '2DAL'   => __('2nd Day Air Letter'),
                '3DS'    => __('3 Day Select'),
                'GND'    => __('Ground'),
                'GNDCOM' => __('Ground Commercial'),
                'GNDRES' => __('Ground Residential'),
                'STD'    => __('Canada Standard'),
                'XPR'    => __('Worldwide Express'),
                'WXS'    => __('Worldwide Express Saver'),
                'XPRL'   => __('Worldwide Express Letter'),
                'XDM'    => __('Worldwide Express Plus'),
                'XDML'   => __('Worldwide Express Plus Letter'),
                'XPD'    => __('Worldwide Expedited'),
            ),

            'pickup'=>array(
                'RDP'    => __('Regular Daily Pickup'),
                'OCA'    => __('On Call Air'),
                'OTP'    => __('One Time Pickup'),
                'LC'     => __('Letter Center'),
                'CC'     => __('Customer Counter'),
            ),

            'container'=>array(
                'CP'     => '00', // Customer Packaging
                'ULE'    => '01', // UPS Letter Envelope
                'UT'     => '03', // UPS Tube
                'UEB'    => '21', // UPS Express Box
                'UW25'   => '24', // UPS Worldwide 25 kilo
                'UW10'   => '25', // UPS Worldwide 10 kilo
            ),

            'container_description'=>array(
                'CP'     => __('Customer Packaging'),
                'ULE'    => __('UPS Letter Envelope'),
                'UT'     => __('UPS Tube'),
                'UEB'    => __('UPS Express Box'),
                'UW25'   => __('UPS Worldwide 25 kilo'),
                'UW10'   => __('UPS Worldwide 10 kilo'),
            ),

            'dest_type'=>array(
                'RES'    => '1', // Residential
                'COM'    => '2', // Commercial
            ),

            'dest_type_description'=>array(
                'RES'    => __('Residential'),
                'COM'    => __('Commercial'),
            )
        );

        if (!isset($codes[$type])) {
//            throw Mage::exception('Mage_Shipping', __('Invalid UPS CGI code type: %s', $type));
            return false;
        } elseif (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
//            throw Mage::exception('Mage_Shipping', __('Invalid UPS CGI code for type %s: %s', $type, $code));
            return false;
        } else {
            return $codes[$type][$code];
        }
    }

    protected function _getXmlQuotes()
    {

    }

}
