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
    public function setUp()
    {
        $this->_markTestAsSoapOnly();
        parent::setUp();
    }

    public function testPerameterizedServiceException()
    {
        $serviceInfo = array(
            'soap' => array(
                'service' => 'testModule3Error',
                'serviceVersion' => 'V1',
                'operation' => 'testModule3ErrorParameterizedServiceException'
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
            $this->fail("SoapFault was not raised as expected.");
        } catch (SoapFault $e) {
            $this->assertEquals('Parameterized service exception', $e->getMessage(), "Fault message is invalid.");
            /** Check SOAP fault details */
            $this->assertNotNull($e->detail, "Details must be present.");
            $expectedParams = array('key1' => 'value1', 'key2' => 'value2');
            $this->assertEquals(
                $expectedParams,
                (array)$e->detail->Parameters,
                "Parameters in fault details are invalid."
            );
            $this->assertEquals(1234, $e->detail->ErrorCode, "Error code in fault details is invalid.");
            /** Check SOAP fault code */
            $this->assertNotNull($e->faultcode, "Fault code must not be empty.");
            $this->assertEquals('env:Sender', $e->faultcode, "Fault code is invalid.");
        }
    }

    public function testWebapiException()
    {
        $serviceInfo = array(
            'soap' => array(
                'service' => 'testModule3Error',
                'serviceVersion' => 'V1',
                'operation' => 'testModule3ErrorWebapiException'
            )
        );
        try {
            $this->_webApiCall($serviceInfo);
            $this->fail("SoapFault was not raised as expected.");
        } catch (SoapFault $e) {
            $this->assertEquals('Service not found', $e->getMessage(), "Fault message is invalid.");
            /** Check SOAP fault details */
            $this->assertNotNull($e->detail, "Details must be present.");
            $this->assertNull($e->detail->Parameters, "Parameters are not expected in fault details.");
            $this->assertEquals(
                Mage_Webapi_Exception::HTTP_NOT_FOUND,
                $e->detail->ErrorCode,
                "Error code in fault details is invalid."
            );

            /** Check SOAP fault code */
            $this->assertNotNull($e->faultcode, "Fault code must not be empty.");
            $this->assertEquals('env:Sender', $e->faultcode, "Fault code is invalid.");
        }
    }

    public function testUnknownException()
    {
        $serviceInfo = array(
            'soap' => array(
                'service' => 'testModule3Error',
                'serviceVersion' => 'V1',
                'operation' => 'testModule3ErrorOtherException'
            )
        );
        try {
            $this->_webApiCall($serviceInfo);
            $this->fail("SoapFault was not raised as expected.");
        } catch (SoapFault $e) {
            $this->assertContains(
                'Internal Error. Details are available in Magento log file. Report ID:',
                $e->getMessage(),
                "Fault message is invalid."
            );
            /** Check SOAP fault details */
            $this->assertNotNull($e->detail, "Details must be present.");
            $this->assertNull($e->detail->Parameters, "Parameters are not expected in fault details.");
            $this->assertEquals(500, $e->detail->ErrorCode, "Error code in fault details is invalid.");

            /** Check SOAP fault code */
            $this->assertNotNull($e->faultcode, "Fault code must not be empty.");
            $this->assertEquals('env:Receiver', $e->faultcode, "Fault code is invalid.");
        }
    }
}
