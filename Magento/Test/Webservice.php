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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Magento_Test_Webservice extends PHPUnit_Framework_TestCase
{

    protected static $_webServices = array();

    const SOAP = 'soap';
    const SOAPV2 = 'soapv2';
    const XMLRPC = 'xmlrpc';

    private $_webServiceMap = array('soap'=>'Magento_Test_Webservice_SoapV1',
                                    'soapv1'=>'Magento_Test_Webservice_SoapV1',
                                    'soapv2'=>'Magento_Test_Webservice_SoapV2',
                                    'xmlrpc'=>'Magento_Test_Webservice_XmlRpc'
                             );


    public function getWebService($type = SOAP)
    {
        if (!array_key_exists($type, self::$_webServices)) {
            $class = $this->_webServiceMap[strtolower($type)];
            self::$_webServices[$type] = new $class();
            self::$_webServices[$type]->init();
        }

        return self::$_webServices[$type];
    }

    public function call($path, $params = array())
    {
      //  $this->getWebService(SOAP);
        $this->getWebService(XMLRPC);
     //   $this->getWebService(SOAPV2);
        
        $result = array();
        foreach(self::$_webServices as $type => $ws)
        {
            $result[$type] = $ws->call($path,$params);
        }

        return $result;
    }
}