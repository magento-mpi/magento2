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
        $this->_markTestAsSoapOnly();
        $serviceInfo = array(
            'soap' => array(
                'service' => 'testModule3Error',
                'serviceVersion' => 'V1',
                'operation' => 'testModule3ErrorParameterizedException'
            )
        );
        $arguments = array(
            'details' => array(
                array('key' => 'key1', 'value' => 'value1'),
                array('key' => 'key2', 'value' => 'value2')
            )
        );
        try {
            $this->_webApiCall($serviceInfo, $arguments);
            $this->fail("Expected SoapFault was not raised.");
        } catch (SoapFault $e) {
            /** Check SOAP fault details */
            $this->assertNotNull($e->detail, "Details must be present.");
            $expectedParams = array('key1' => 'value1', 'key2' => 'value2');
            $expectedErrorCode = 1234;
            $this->assertEquals(
                $expectedParams,
                (array)$e->detail->Parameters,
                "Parameters in fault details are invalid."
            );
            $this->assertEquals($expectedErrorCode, $e->detail->ErrorCode, "Error code in fault details is invalid.");
            $this->assertEquals('Parameterized service exception', $e->getMessage(), "Fault message is invalid.");
            /** Check SOAP fault code */
            $this->assertNotNull($e->faultcode, "Fault code must not be empty.");
            $this->assertEquals('env:Receiver', $e->faultcode, "Fault code is invalid.");
        }
    }
}
