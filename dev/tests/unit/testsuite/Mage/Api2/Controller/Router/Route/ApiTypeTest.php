<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Api2_Controller_Router_Route_ApiTypeTest extends PHPUnit_Framework_TestCase
{
    public function testMatch()
    {
        $route = new Mage_Api2_Controller_Router_Route_ApiType();

        $testApiType = 'test_api';
        $testUri = str_replace(':api_type', $testApiType, Mage_Api2_Controller_Router_Route_ApiType::API_ROUTE);
        $request = new Mage_Api2_Model_Request();
        $request->setRequestUri($testUri);

        $match = $route->match($request);
        $this->assertEquals($testApiType, $match['api_type']);
    }
}
