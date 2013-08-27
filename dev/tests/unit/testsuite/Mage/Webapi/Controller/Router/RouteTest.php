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

class Mage_Webapi_Controller_Router_RouteTest extends PHPUnit_Framework_TestCase
{
    public function testMatch()
    {
        $areaName = 'webapi';
        $route = new Mage_Webapi_Controller_Router_Route(
            $areaName . '/:' . Mage_Webapi_Controller_Request::PARAM_REQUEST_TYPE
        );

        $testApiType = 'test_api';
        $testUri = "$areaName/$testApiType";
        $request = new Zend_Controller_Request_Http();
        $request->setRequestUri($testUri);

        $match = $route->match($request);
        $this->assertEquals($testApiType, $match['request_type']);
    }
}
