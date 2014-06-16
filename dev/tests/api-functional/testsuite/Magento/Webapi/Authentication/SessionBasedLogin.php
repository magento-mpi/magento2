<?php
/**
 * Test Session based authentication for APIs.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Authentication;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Helper\Customer as CustomerHelper;

class SessionBasedLogin extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    /**
     * @var \Magento\TestFramework\TestCase\Webapi\Adapter\Rest\CurlClient;
     */
    protected $curlClient;

    /**#@+
     * Constants for endpoints
     */
    const LOGIN_REGISTERED = '/api/login';
    const LOGIN_ANONYMOUS = '/api/login/anonymous';
    /**#@-*/

    const CONTENT_TYPE_JSON = 'application/json';
    const CONTENT_TYPE_XML = 'application/xml';

    /** @var CustomerHelper */
    private $customerHelper;

    /** @var array */
    private $customerData;

    /**
     * Setup curl client
     *
     * @return void
     */
    public function setUp()
    {
        $this->_markTestAsRestOnly();
        $this->curlClient = new \Magento\TestFramework\TestCase\Webapi\Adapter\Rest\CurlClient();
        $this->curlClient->setRestBasePath('');
        $this->customerHelper = Bootstrap::getObjectManager()->create('Magento\TestFramework\Helper\Customer');
        $this->customerData = $this->customerHelper->createSampleCustomer();
    }

    /**
     * @dataProvider contentTypeProvider
     *
     * @param $contentType
     */
    public function testLogin($contentType)
    {
        $arguments = ["username" => $this->customerData['email'], "password" => CustomerHelper::PASSWORD];
        $this->login(self::LOGIN_REGISTERED, $arguments, $contentType);
    }

    public function testLoginAnonymous()
    {
        $this->login(self::LOGIN_ANONYMOUS, null, null);
    }

    /**
     * Get content type
     *
     * @return array
     */
    public function contentTypeProvider()
    {
        return [
            ['json' => self::CONTENT_TYPE_JSON],
            ['xml' => self::CONTENT_TYPE_XML]
        ];
    }

    protected function login($resource, $arguments, $argumentContentType)
    {
        $headers = [];
        $data = null;
        if ($arguments) {
            if ($argumentContentType == self::CONTENT_TYPE_JSON) {
                $data = $arguments;
            } else {
                //Need to create a utility to parse array as xml
                $data = "<login><username>" . $arguments['username'] . "</username><password>"
                    . $arguments['password'] . "</password></login>";
            }
        }
        $headers[] = 'Content-Type: ' . $argumentContentType;
        $response = $this->curlClient->post($resource, $data, $headers);
        //Make sure response is null/empty
        $this->assertNull($response);
        //Check for Set-Cookie to make sure correct header will be set
        $this->assertContains("Set-Cookie: frontend=", $this->curlClient->getCurrentResponseArray()['header']);
    }

}

