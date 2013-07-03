<?php
/**
 * Test Web API error codes.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_HttpErrorCodeTest extends Magento_Test_TestCase_WebapiAbstract
{
    public function testSuccess()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/success',
                'httpMethod' => 'GET'
            ),
        );

        $item = $this->_webApiCall($serviceInfo);

         // TODO: check Http Status = 200, cannot do yet due to missing header info returned

        $this->assertEquals('a good id', $item['id'], 'Success case is correct');
    }

    public function testNotFound()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/notfound',
                'httpMethod' => 'GET'
            ),
        );

        $this->_errorTest($serviceInfo, 404, 2345, 'Resource not found');
    }

    public function testUnauthorized()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/unauthorized',
                'httpMethod' => 'GET'
            ),
        );

        $this->_errorTest($serviceInfo, 401, 4567, 'Service authorization exception');
    }

    public function testServiceException()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/serviceexception',
                'httpMethod' => 'GET'
            ),
        );

        $this->_errorTest($serviceInfo, 400, 3456, 'Generic service exception');
    }

    public function testOtherException()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/otherexception',
                'httpMethod' => 'GET'
            ),
        );

        $this->_errorTest($serviceInfo, 500, 5678, 'Non service exception');
    }

    private function _errorTest ($serviceInfo, $httpStatus, $errorCode, $errorMessage)
    {
        // TODO: need to get header info instead of catching the exception
        try {
            $item = $this->_webApiCall($serviceInfo);
        } catch (Exception $e) {
            $this->assertEquals($httpStatus, $e->getCode(), 'Checking HTTP status code');

            $body = json_decode($e->getMessage(), true);
            $this->assertEquals($errorCode, $body['errors'][0]['code'], 'Checking body code');
            $this->assertEquals($errorMessage, $body['errors'][0]['message'], 'Checking body message');
        }
    }
}
