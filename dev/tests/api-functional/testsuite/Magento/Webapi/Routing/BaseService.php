<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Routing;

/**
 * Base class for all Service based routing tests
 */
abstract class BaseService extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    /**
     * Check a particular adapter and assert unauthorized access
     *
     * @param array      $serviceInfo
     * @param array|null $requestData
     */
    protected function assertUnauthorizedException($serviceInfo, $requestData = null)
    {
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $this->_assertSoapException($serviceInfo, $requestData, 'Not Authorized.');
        } else if (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST) {
            $this->_assertRestUnauthorizedException($serviceInfo, $requestData);
        }
    }

    /**
     * Invoke the REST api and assert access is unauthorized
     *
     * @param array      $serviceInfo
     * @param array|null $requestData
     */
    protected function _assertRestUnauthorizedException($serviceInfo, $requestData = null)
    {
        try {
            $this->_webApiCall($serviceInfo, $requestData);
        } catch (\Exception $e) {
            $this->assertContains(
                '{"errors":[{"message":"Not Authorized.","http_code":401',
                $e->getMessage(),
                sprintf(
                    'REST routing did not fail as expected for the method "%s" of service "%s"',
                    $serviceInfo['rest']['httpMethod'],
                    $serviceInfo['rest']['resourcePath']
                )
            );
        }
    }

    /**
     * Check a particular adapter and assert the exception
     *
     * @param array      $serviceInfo
     * @param array|null $requestData
     */
    protected function _assertNoRouteOrOperationException($serviceInfo, $requestData = null)
    {
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $this->_assertSoapException($serviceInfo, $requestData);
        } else if (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST) {
            $this->_assertNoRestRouteException($serviceInfo, $requestData);
        }
    }

    /**
     * Invoke the REST api and assert for test cases that no such REST route exist
     *
     * @param array      $serviceInfo
     * @param array|null $requestData
     */
    protected function _assertNoRestRouteException($serviceInfo, $requestData = null)
    {
        try {
            $this->_webApiCall($serviceInfo, $requestData);
        } catch (\Exception $e) {
            $this->assertContains(
                '{"errors":[{"message":"Request does not match any route.","http_code":404',
                $e->getMessage(),
                sprintf(
                    'REST routing did not fail as expected for the method "%s" of service "%s"',
                    $serviceInfo['rest']['httpMethod'],
                    $serviceInfo['rest']['resourcePath']
                )
            );
        }
    }

    /**
     * Invoke the SOAP api and assert for the NoWebApiXmlTestTest test cases that no such SOAP route exists
     *
     * @param array      $serviceInfo
     * @param array|null $requestData
     * @param string     $expectedMessage
     */
    protected function _assertSoapException($serviceInfo, $requestData = null, $expectedMessage = '')
    {
        try {
            $this->_webApiCall($serviceInfo, $requestData);
        } catch (\Exception $e) {
            if (get_class($e) !== 'SoapFault') {
                $this->fail(sprintf(
                    'Expected SoapFault exception not generated for Service - "%s" and Operation - "%s"',
                    $serviceInfo['soap']['service'],
                    $serviceInfo['soap']['operation']
                ));
            }

            if ($expectedMessage) {
                $this->assertEquals($expectedMessage, $e->getMessage());
            }
        }
    }

}
