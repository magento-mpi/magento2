<?php
/**
 * SOAP error handling test.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Routing_SoapErrorHandlingTest extends Magento_Test_TestCase_WebapiAbstract
{
    public function testSoapException()
    {
        $this->markTestIncomplete('Waiting for MAGETWO-11853');
        $this->_markTestAsSoapOnly();
        $serviceInfo = array(
            'soap' => array(
                'service' => 'testModule3Error',
                'serviceVersion' => 'V1',
                'operation' => 'testModule3ErrorResourceNotFoundException'
            )
        );
        $response = $this->_webApiCall($serviceInfo);
        $expectedResult = array('error' => array('code' => 2345, 'message' => 'Resource not found'));
        $this->assertEquals($expectedResult, $response, 'Invalid error response format');
    }
}
