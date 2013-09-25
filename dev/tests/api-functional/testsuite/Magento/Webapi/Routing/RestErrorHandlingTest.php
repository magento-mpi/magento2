<?php
/**
 * Test Web API error codes.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Routing;

class RestErrorHandlingTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    public function setUp()
    {
        $this->_markTestAsRestOnly();
        parent::setUp();
    }

    public function testSuccess()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/success',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
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
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ),
        );

        // \Magento\Service\ResourceNotFoundException
        $this->_errorTest(
            $serviceInfo,
            array(),
            \Magento\Webapi\Exception::HTTP_NOT_FOUND,
            2345,
            "Resource with ID 'resourceY' not found."
        );
    }

    public function testUnauthorized()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/unauthorized',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ),
        );

        // \Magento\Service\AuthorizationException
        $this->_errorTest(
            $serviceInfo,
            array(),
            \Magento\Webapi\Exception::HTTP_UNAUTHORIZED,
            4567,
            "User with ID '30' is not authorized to access resource with ID 'resourceN'."
        );
    }

    public function testServiceException()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/serviceexception',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ),
        );

        // \Magento\Service\Exception
        $this->_errorTest(
            $serviceInfo,
            array(),
            \Magento\Webapi\Exception::HTTP_BAD_REQUEST,
            3456,
            'Generic service exception'
        );
    }

    public function testServiceExceptionWithParameters()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/parameterizedserviceexception',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            )
        );
        $details = array(
            'key1' => 'value1',
            'key2' => 'value2'
        );
        $arguments = array(
            'details' => $details
        );
        // \Magento\Service\Exception (with parameters)
        $this->_errorTest(
            $serviceInfo,
            $arguments,
            \Magento\Webapi\Exception::HTTP_BAD_REQUEST,
            1234,
            'Parameterized service exception',
            $details
        );
    }

    public function testOtherException()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/otherexception',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ),
        );

        $expectedErrorCodes = array(5678, null);
        $expectedErrorMessages = array(
            'Non service exception',
            'Internal Error. Details are available in Magento log file. Report ID: %1'
        );
        $this->_errorTest(
            $serviceInfo,
            array(),
            \Magento\Webapi\Exception::HTTP_INTERNAL_ERROR,
            $expectedErrorCodes,
            $expectedErrorMessages
        );
    }

    public function testReturnIncompatibleDataType()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/returnIncompatibleDataType',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ),
        );
        $expectedMessages = array(
            'Internal Error. Details are available in Magento log file. Report ID: %1',
            'The method "returnIncompatibleDataType" of service '
            . '"Magento\TestModule3\Service\ErrorV1Interface" must return an array.'
        );
        // \Magento\Service\Exception
        $this->_errorTest(
            $serviceInfo,
            array(),
            \Magento\Webapi\Exception::HTTP_INTERNAL_ERROR,
            0,
            $expectedMessages,
            null
        );
    }

    /**
     * Perform a negative REST api call test case and compare the results with expected values.
     *
     * @param string $serviceInfo - REST Service information (i.e. resource path and HTTP method)
     * @param array $data - Data for the cal
     * @param int $httpStatus - Expected HTTP status
     * @param int|array $errorCode - Expected error code
     * @param string|array $errorMessage - \Exception error message
     * @param array $parameters - Optional parameters array, or null if no parameters
     */
    protected function _errorTest(
        $serviceInfo,
        $data,
        $httpStatus,
        $errorCode,
        $errorMessage,
        $parameters = array()
    ) {
        // TODO: need to get header info instead of catching the exception
        try {
            $this->_webApiCall($serviceInfo, $data);
        } catch (\Exception $e) {
            $this->assertEquals($httpStatus, $e->getCode(), 'Checking HTTP status code');

            $body = json_decode($e->getMessage(), true);

            $errorCodes = is_array($errorCode) ? $errorCode : array($errorCode);
            if (isset($body['errors'][0]['code'])) {
                $this->assertTrue(
                    in_array(
                        $body['errors'][0]['code'],
                        $errorCodes
                    ),
                    sprintf(
                        "Error code is invalid. Actual: {$body['errors'][0]['code']}, Expected one of: \n%s",
                        implode("\n", $errorCodes)
                    )
                );
            } else {
                $this->assertTrue(in_array(0, $errorCodes), "Error code was expected");
            }

            $errorMessages = is_array($errorMessage) ? $errorMessage : array($errorMessage);
            $this->assertTrue(
                in_array($body['errors'][0]['message'], $errorMessages),
                "Message is invalid. Actual: {$body['errors'][0]['message']}. Expected one of:" .
                implode("\n", $errorMessages)
            );

            if ($parameters) {
                $this->assertEquals($parameters, $body['errors'][0]['parameters'], 'Checking body parameters');
            }
        }
    }
}
