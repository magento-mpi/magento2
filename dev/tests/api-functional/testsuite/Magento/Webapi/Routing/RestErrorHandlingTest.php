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

use Magento\Webapi\Exception as WebapiException;

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
            )
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
            )
        );

        // \Magento\Framework\Api\ResourceNotFoundException
        $this->_errorTest(
            $serviceInfo,
            ['resource_id' => 'resourceY'],
            WebapiException::HTTP_NOT_FOUND,
            'Resource with ID "%resource_id" not found.'
        );
    }

    public function testUnauthorized()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/unauthorized',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            )
        );

        // \Magento\Framework\Api\AuthorizationException
        $this->_errorTest(
            $serviceInfo,
            [],
            WebapiException::HTTP_UNAUTHORIZED,
            'Consumer is not authorized to access %resources',
            ['resources' => 'resourceN']
        );
    }

    public function testOtherException()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/errortest/otherexception',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            )
        );

        $expectedMessage = 'Internal Error. Details are available in Magento log file. Report ID: webapi-XXX';
        $this->_errorTest(
            $serviceInfo,
            [],
            WebapiException::HTTP_INTERNAL_ERROR,
            $expectedMessage
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
    protected function _errorTest($serviceInfo, $data, $httpStatus, $errorMessage, $parameters = array())
    {
        // TODO: need to get header info instead of catching the exception
        try {
            $this->_webApiCall($serviceInfo, $data);
        } catch (\Exception $e) {
            $this->assertEquals($httpStatus, $e->getCode(), 'Checking HTTP status code');

            $body = json_decode($e->getMessage(), true);

            $errorMessages = is_array($errorMessage) ? $errorMessage : array($errorMessage);
            $actualMessage = $body['message'];
            $matches = array();
            //Report ID was created dynamically, so we need to replace it with some static value in order to test
            if (preg_match('/.*Report\sID\:\s([a-zA-Z0-9\-]*)/', $actualMessage, $matches)) {
                $actualMessage = str_replace($matches[1], 'webapi-XXX', $actualMessage);
            }
            //make sure that the match for a report with an id is found if Internal error was reported
            //Refer : \Magento\Webapi\Controller\ErrorProcessor::INTERNAL_SERVER_ERROR_MSG
            if (count($matches) > 1) {
                $this->assertTrue(!empty($matches[1]), 'Report id missing for internal error.');
            }
            $this->assertContains(
                $actualMessage,
                $errorMessages,
                "Message is invalid. Actual: '{$actualMessage}'. Expected one of: {'" . implode(
                    "', '",
                    $errorMessages
                ) . "'}"
            );

            if ($parameters) {
                $this->assertEquals($parameters, $body['parameters'], 'Checking body parameters');
            }
        }
    }
}
