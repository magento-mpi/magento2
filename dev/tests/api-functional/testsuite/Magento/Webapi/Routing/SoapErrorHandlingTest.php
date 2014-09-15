<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Routing;

use Magento\Framework\Exception\AuthorizationException;
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
                'env:Sender'
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

    public function testEmptyInputException()
    {
        $parameters = [];
        $this->_testWrappedError($parameters);
    }

    public function testSingleWrappedErrorException()
    {
        $parameters = [
            ['fieldName' => 'key1', 'value' => 'value1']
        ];
        $this->_testWrappedError($parameters);
    }

    public function testMultipleWrappedErrorException()
    {
        $parameters = [
            ['fieldName' => 'key1', 'value' => 'value1'],
            ['fieldName' => 'key2', 'value' => 'value2']
        ];
        $this->_testWrappedError($parameters);
    }

    protected function _testWrappedError($parameters)
    {
        $serviceInfo = [
            'soap' => [
                'service' => 'testModule3ErrorV1',
                'operation' => 'testModule3ErrorV1InputException'
            ]
        ];

        $expectedException = new \Magento\Framework\Exception\InputException();
        foreach ($parameters as $error) {
            $expectedException->addError(\Magento\Framework\Exception\InputException::INVALID_FIELD_VALUE, $error);
        }

        $arguments = [
            'wrappedErrorParameters' => $parameters
        ];

        $expectedErrors = [];
        foreach ($expectedException->getErrors() as $key => $error) {
            $expectedErrors[$key] = [
                'message' => $error->getRawMessage(),
                'params' => $error->getParameters()
            ];
        }


        try {
            $this->_webApiCall($serviceInfo, $arguments);
            $this->fail("SoapFault was not raised as expected.");
        } catch (\SoapFault $e) {
            $this->_checkSoapFault(
                $e,
                $expectedException->getRawMessage(),
                'env:Sender',
                $expectedException->getParameters(), // expected error parameters
                false,
                $expectedErrors                      // expected wrapped errors
            );
        }
    }

    public function testUnauthorized()
    {
        $serviceInfo = [
            'soap' => [
                'service' => 'testModule3ErrorV1',
                'operation' => 'testModule3ErrorV1AuthorizationException',
                'token' => 'invalidToken'
            ]
        ];

        $expectedException = new AuthorizationException(
            AuthorizationException::NOT_AUTHORIZED,
            ['resources' => 'Magento_TestModule3::resource1, Magento_TestModule3::resource2']
        );

        try {
            $this->_webApiCall($serviceInfo);
            $this->fail("SoapFault was not raised as expected.");
        } catch (\SoapFault $e) {
            $this->_checkSoapFault(
                $e,
                $expectedException->getRawMessage(),
                'env:Sender',
                $expectedException->getParameters() // expected error parameters
            );
        }
    }

    /**
     * Verify that SOAP fault contains necessary information.
     *
     * @param \SoapFault $soapFault
     * @param string $expectedMessage
     * @param string $expectedFaultCode
     * @param array $expectedErrorParams
     * @param bool $isTraceExpected
     * @param array $expectedWrappedErrors
     */
    protected function _checkSoapFault(
        $soapFault,
        $expectedMessage,
        $expectedFaultCode,
        $expectedErrorParams = array(),
        $isTraceExpected = false,
        $expectedWrappedErrors = array()
    ) {
        $this->assertContains($expectedMessage, $soapFault->getMessage(), "Fault message is invalid.");

        $errorDetailsNode = 'GenericFault';
        $errorDetails = isset($soapFault->detail->$errorDetailsNode) ? $soapFault->detail->$errorDetailsNode : null;
        if (!empty($expectedErrorParams) || $isTraceExpected || !empty($expectedWrappedErrors)) {
            /** Check SOAP fault details */
            $this->assertNotNull($errorDetails, "Details must be present.");
            $this->_checkFaultParams($expectedErrorParams, $errorDetails);
            $this->_checkWrappedErrors($expectedWrappedErrors, $errorDetails);

            /** Check error trace */
            $traceNode = Fault::NODE_DETAIL_TRACE;
            $mode = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\App\State')
                ->getMode();
            if ($mode != \Magento\Framework\App\State::MODE_DEVELOPER) {
                /** Developer mode changes tested behavior and it cannot properly be tested for now */
                if ($isTraceExpected) {
                    $this->assertTrue(isset($errorDetails->$traceNode), "Exception trace was expected.");
                } else {
                    $this->assertFalse(isset($errorDetails->$traceNode), "Exception trace was not expected.");
                }
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

    /**
     * Check additional wrapped errors.
     *
     * @param array $expectedWrappedErrors
     * @param \stdClass $errorDetails
     */
    protected function _checkWrappedErrors($expectedWrappedErrors, $errorDetails)
    {
        $wrappedErrorsNode = Fault::NODE_DETAIL_WRAPPED_ERRORS;
        if ($expectedWrappedErrors) {
            $wrappedErrorNode = Fault::NODE_DETAIL_WRAPPED_ERROR;
            $wrappedErrorNodeFieldName = 'fieldName';
            $wrappedErrorNodeValue = Fault::NODE_DETAIL_WRAPPED_ERROR_VALUE;
            $actualWrappedErrors = array();
            if (isset($errorDetails->$wrappedErrorsNode->$wrappedErrorNode)) {
                if (is_array($errorDetails->$wrappedErrorsNode->$wrappedErrorNode)) {
                    foreach ($errorDetails->$wrappedErrorsNode->$wrappedErrorNode as $error) {
                        $actualParameters = [];
                        foreach ($error->parameters->parameter as $parameter) {
                            $actualParameters[$parameter->key] = $parameter->value;
                        }
                        $actualWrappedErrors[] = [
                            'message' => $error->message,
                            'params' => $actualParameters,
                        ];
                    }
                } else {
                    $error = $errorDetails->$wrappedErrorsNode->$wrappedErrorNode;
                    $actualWrappedErrors[] = [
                        "fieldName" => $error->$wrappedErrorNodeFieldName,
                        "value" => $error->$wrappedErrorNodeValue
                    ];
                }
            }
            $this->assertEquals(
                $expectedWrappedErrors,
                $actualWrappedErrors,
                "Wrapped errors in fault details are invalid."
            );
        } else {
            $this->assertFalse(
                isset($errorDetails->$wrappedErrorsNode),
                "Wrapped errors are not expected in fault details."
            );
        }
    }
}
