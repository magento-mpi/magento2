<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi;

use Magento\TestFramework\TestCase\Webapi\Adapter\Rest\CurlClient;
use Magento\Webapi\Model\Rest\Config as RestConfig;

class DeserializationTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    /**
     * @var string
     */
    protected $_version;

    /**
     * @var string
     */
    protected $_restResourcePath;

    protected function setUp()
    {
        $this->_version = 'V1';
        $this->_restResourcePath = "/{$this->_version}/TestModule5/";
    }

    /**
     *  Test POST request with empty body
     */
    public function testPostRequestWithEmptyBody()
    {
        $this->_markTestAsRestOnly();
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath,
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            )
        );
        $expectedMessage = 'Request body should not be empty.';
        try {
            $this->_webApiCall($serviceInfo, CurlClient::EMPTY_REQUEST_BODY);
        } catch (\Exception $e) {
            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "Response does not contain expected message."
            );
        }
    }

    /**
     *  Test PUT request with empty body
     */
    public function testPutRequestWithEmptyBody()
    {
        $this->_markTestAsRestOnly();
        $itemId = 1;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            )
        );
        $expectedMessage = 'Request body should not be empty.';
        try {
            $this->_webApiCall($serviceInfo, CurlClient::EMPTY_REQUEST_BODY);
        } catch (\Exception $e) {
            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "Response does not contain expected message."
            );
        }
    }
}