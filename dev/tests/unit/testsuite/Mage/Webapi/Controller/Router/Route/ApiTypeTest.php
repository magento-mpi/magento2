<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Webapi_Controller_Router_Route_WebapiTest extends PHPUnit_Framework_TestCase
{
    public function testMatch()
    {
        $route = new Mage_Webapi_Controller_Router_Route_Webapi(
            Mage_Webapi_Controller_Router_Route_Webapi::getApiRoute());

        $testApiType = 'test_api';
        $testUri = str_replace(':api_type', $testApiType, Mage_Webapi_Controller_Router_Route_Webapi::getApiRoute());
        $request = new Zend_Controller_Request_Http();
        $request->setRequestUri($testUri);

        $match = $route->match($request);
        $this->assertEquals($testApiType, $match['api_type']);
    }
}
