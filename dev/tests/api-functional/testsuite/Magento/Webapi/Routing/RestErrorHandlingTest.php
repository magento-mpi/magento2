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
    protected function setUp()
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

        $this->assertEquals('a good id', $item['value'], 'Success case is correct');
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
            'Generic service exception'
        );
    }

    public function testServiceExceptionWithParameters()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/parameterizedserviceexception',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            )
        );
        $arguments = array(
            'parameters' => array(
                array('name' => 'key1', 'value' => 'value1'),
                array('name' => 'key2', 'value' => 'value2'),
            )
        );
        $expectedExceptionParameters = array('key1' => 'value1', 'key2' => 'value2');
        // \Magento\Service\Exception (with parameters)
        $this->_errorTest(
            $serviceInfo,
            $arguments,
            \Magento\Webapi\Exception::HTTP_BAD_REQUEST,
            'Parameterized service exception',
            $expectedExceptionParameters
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

        $expectedMessages = array(
            'Non service exception',
            'Internal Error. Details are available in Magento log file. Report ID: webapi-XXX'
        );
        $this->_errorTest(
            $serviceInfo,
            array(),
            \Magento\Webapi\Exception::HTTP_INTERNAL_ERROR,
            $expectedMessages
        );
    }

    /**
     * Perform a negative REST api call test case and compare the results with expected values.
     *
     * @param string $serviceInfo - REST Service information (i.e. resource path and HTTP method)
     * @param array $data - Data for the cal
     * @param int $httpStatus - Expected HTTP status
     * @param string|array $errorMessage - \Exception error message
     * @param array $parameters - Optional parameters array, or null if no parameters
     */
    protected function _errorTest(
        $serviceInfo,
        $data,
        $httpStatus,
        $errorMessage,
        $parameters = array()
    ) {
        // TODO: need to get header info instead of catching the exception
        try {
            $this->_webApiCall($serviceInfo, $data);
        } catch (\Exception $e) {
            $this->assertEquals($httpStatus, $e->getCode(), 'Checking HTTP status code');

            $body = json_decode($e->getMessage(), true);

            $errorMessages = is_array($errorMessage) ? $errorMessage : array($errorMessage);
            $actualMessage = $body['errors'][0]['message'];
            $matches = [];
            //Report ID was created dynamically, so we need to replace it with some static value in order to test
            if (preg_match('/.*Report\sID\:\s([a-zA-Z0-9\-]*)/', $actualMessage, $matches)) {
                $actualMessage = str_replace($matches[1], 'webapi-XXX', $actualMessage);
            }
            $this->assertContains(
                $actualMessage, $errorMessages,
                "Message is invalid. Actual: '$actualMessage'. Expected one of: {'" .
                implode("', '", $errorMessages) . "'}"
            );

            if ($parameters) {
                $this->assertEquals($parameters, $body['errors'][0]['parameters'], 'Checking body parameters');
            }
        }
    }
}
