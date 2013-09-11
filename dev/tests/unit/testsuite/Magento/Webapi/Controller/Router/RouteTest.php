<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Webapi_Controller_Router_RouteTest extends PHPUnit_Framework_TestCase
{
    public function testMatch()
    {
        $areaName = 'webapi';
        $route = new \Magento\Webapi\Controller\Router\Route(
            $areaName . '/:' . \Magento\Webapi\Controller\Request::PARAM_API_TYPE
        );

        $testApiType = 'test_api';
        $testUri = "$areaName/$testApiType";
        $request = new Zend_Controller_Request_Http();
        $request->setRequestUri($testUri);

        $match = $route->match($request);
        $this->assertEquals($testApiType, $match['api_type']);
    }
}
