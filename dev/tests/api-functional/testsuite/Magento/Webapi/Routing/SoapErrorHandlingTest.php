<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Routing;

use Magento\Webapi\Model\Soap\Fault;

/**
 * SOAP error handling test.
 */
class SoapErrorHandlingTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    protected function setUp()
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
            'parameters' => array(
                array('name' => 'key1', 'value' => 'value1'),
                array('name' => 'key2', 'value' => 'value2')
            )
        );
        try {
            $this->_webApiCall($serviceInfo, $arguments);
            $this->fail("SoapFault was not raised as expected.");
        } catch (\SoapFault $e) {
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
        } catch (\SoapFault $e) {
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
        } catch (\SoapFault $e) {
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

    /**
     * Verify that SOAP fault contains necessary information.
     *
     * @param \SoapFault $soapFault
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

        $errorDetailsNode = Fault::NODE_DETAIL_WRAPPER;
        $errorDetails = isset($soapFault->detail->$errorDetailsNode) ? $soapFault->detail->$errorDetailsNode : null;
        if (!is_null($expectedErrorCode) || !empty($expectedErrorParams) || $isTraceExpected) {
            /** Check SOAP fault details */
            $this->assertNotNull($errorDetails, "Details must be present.");
            $this->_checkFaultParams($expectedErrorParams, $errorDetails);

            /** Check error trace */
            $traceNode = Fault::NODE_DETAIL_TRACE;
            $mode = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')
                ->getMode();
            if ($mode != \Magento\App\State::MODE_DEVELOPER) {
                /** Developer mode changes tested behavior and it cannot properly be tested for now */
                if ($isTraceExpected) {
                    $this->assertTrue(isset($errorDetails->$traceNode), "Exception trace was expected.");
                } else {
                    $this->assertFalse(isset($errorDetails->$traceNode), "Exception trace was not expected.");
                }
            }

            /** Check error code if present*/
            if ($expectedErrorCode) {
                $this->assertEquals(
                    $expectedErrorCode,
                    $errorDetails->{Fault::NODE_DETAIL_CODE},
                    "Error code in fault details is invalid."
                );
            }

        } else {
            $this->assertNull($errorDetails, "Details are not expected.");
        }

        /** Check SOAP fault code */
        $this->assertNotNull($soapFault->faultcode, "Fault code must not be empty.");
        $this->assertEquals($expectedFaultCode, $soapFault->faultcode, "Fault code is invalid.");
    }

    /**
     * Check additional error parameters.
     *
     * @param array $expectedErrorParams
     * @param \stdClass $errorDetails
     */
    protected function _checkFaultParams($expectedErrorParams, $errorDetails)
    {
        $paramsNode = Fault::NODE_DETAIL_PARAMETERS;
        if ($expectedErrorParams) {
            $paramNode = Fault::NODE_DETAIL_PARAMETER;
            $paramKey = Fault::NODE_DETAIL_PARAMETER_KEY;
            $paramValue = Fault::NODE_DETAIL_PARAMETER_VALUE;
            $actualParams = array();
            if (isset($errorDetails->$paramsNode->$paramNode)) {
                if (is_array($errorDetails->$paramsNode->$paramNode)) {
                    foreach ($errorDetails->$paramsNode->$paramNode as $param) {
                        $actualParams[$param->$paramKey] = $param->$paramValue;
                    }
                } else {
                    $param = $errorDetails->$paramsNode->$paramNode;
                    $actualParams[$param->$paramKey] = $param->$paramValue;
                }
            }
            $this->assertEquals(
                $expectedErrorParams,
                $actualParams,
                "Parameters in fault details are invalid."
            );
        } else {
            $this->assertFalse(isset($errorDetails->$paramsNode), "Parameters are not expected in fault details.");
        }
    }
}
