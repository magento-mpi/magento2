<?php
/**
 * SOAP error handling test.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Routing_SoapErrorHandlingTest extends Magento_Test_TestCase_WebapiAbstract
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
                'service' => 'testModule3ErrorV1',
                'operation' => 'testModule3ErrorV1ParameterizedServiceException'
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
            $this->_checkSoapFault(
                $e,
                'Parameterized service exception',
                'env:Sender',
                1234,
                array('key1' => 'value1', 'key2' => 'value2')
            );
        }
    }

    public function testWebapiException()
    {
        $serviceInfo = array(
            'soap' => array(
                'service' => 'testModule3ErrorV1',
                'operation' => 'testModule3ErrorV1WebapiException'
            )
        );
        try {
            $this->_webApiCall($serviceInfo);
            $this->fail("SoapFault was not raised as expected.");
        } catch (SoapFault $e) {
            $this->_checkSoapFault(
                $e,
                'Service not found',
                'env:Sender',
                5555
            );
        }
    }

    public function testUnknownException()
    {
        $serviceInfo = array(
            'soap' => array(
                'service' => 'testModule3ErrorV1',
                'operation' => 'testModule3ErrorV1OtherException'
            )
        );
        try {
            $this->_webApiCall($serviceInfo);
            $this->fail("SoapFault was not raised as expected.");
        } catch (SoapFault $e) {
            /** In developer mode message is masked, so checks should be different in two modes */
            if (strpos($e->getMessage(), 'Internal Error') === false) {
                $this->_checkSoapFault(
                    $e,
                    'Non service exception',
                    'env:Receiver',
                    5678
                );
            } else {
                $this->_checkSoapFault(
                    $e,
                    'Internal Error. Details are available in Magento log file. Report ID:',
                    'env:Receiver'
                );
            }
        }
    }

    public function testReturnIncompatibleDataType()
    {
        $serviceInfo = array(
            'soap' => array(
                'service' => 'testModule3ErrorV1',
                'operation' => 'testModule3ErrorV1ReturnIncompatibleDataType'
            )
        );
        try {
            $this->_webApiCall($serviceInfo);
            $this->fail("SoapFault was not raised as expected.");
        } catch (SoapFault $e) {
            /** In developer mode message is masked, so checks should be different in two modes */
            if (strpos($e->getMessage(), 'Internal Error') === false) {
                $this->_checkSoapFault(
                    $e,
                    'Non service exception',
                    'env:Receiver'
                );
            } else {
                $this->_checkSoapFault(
                    $e,
                    'Internal Error. Details are available in Magento log file. Report ID:',
                    'env:Receiver'
                );
            }
        }
    }

    /**
     * Verify that SOAP fault contains necessary information.
     *
     * @param SoapFault $soapFault
     * @param string $expectedMessage
     * @param string $expectedFaultCode
     * @param string $expectedErrorCode
     * @param array $expectedErrorParams
     * @param bool $isTraceExpected
     */
    protected function _checkSoapFault(
        $soapFault,
        $expectedMessage,
        $expectedFaultCode,
        $expectedErrorCode = null,
        $expectedErrorParams = array(),
        $isTraceExpected = false
    ) {
        $this->assertContains($expectedMessage, $soapFault->getMessage(), "Fault message is invalid.");

        $errorDetailsNode = Magento_Webapi_Model_Soap_Fault::NODE_ERROR_DETAILS;
        $errorDetails = isset($soapFault->detail->$errorDetailsNode) ? $soapFault->detail->$errorDetailsNode : null;
        if (!is_null($expectedErrorCode) || !empty($expectedErrorParams) || $isTraceExpected) {
            /** Check SOAP fault details */
            $this->assertNotNull($errorDetails, "Details must be present.");

            /** Check additional error parameters */
            $paramsNode = Magento_Webapi_Model_Soap_Fault::NODE_ERROR_DETAIL_PARAMETERS;
            if ($expectedErrorParams) {
                $this->assertEquals(
                    $expectedErrorParams,
                    (array)$errorDetails->$paramsNode,
                    "Parameters in fault details are invalid."
                );
            } else {
                $this->assertFalse(isset($errorDetails->$paramsNode), "Parameters are not expected in fault details.");
            }

            /** Check error trace */
            $traceNode = Magento_Webapi_Model_Soap_Fault::NODE_ERROR_DETAIL_TRACE;
            if (!Mage::app()->isDeveloperMode()) {
                /** Developer mode changes tested behavior and it cannot properly be tested for now */
                if ($isTraceExpected) {
                    $this->assertNotNull($errorDetails->$traceNode, "Exception trace was expected.");
                } else {
                    $this->assertNull($errorDetails->$traceNode, "Exception trace was not expected.");
                }
            }

            /** Check error code if present*/
            $this->assertEquals(
                $expectedErrorCode,
                $errorDetails->{Magento_Webapi_Model_Soap_Fault::NODE_ERROR_DETAIL_CODE},
                "Error code in fault details is invalid."
            );

        } else {
            $this->assertNull($errorDetails, "Details are not expected.");
        }

        /** Check SOAP fault code */
        $this->assertNotNull($soapFault->faultcode, "Fault code must not be empty.");
        $this->assertEquals($expectedFaultCode, $soapFault->faultcode, "Fault code is invalid.");
    }
}
