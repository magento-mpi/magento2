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
    const CUSTOMER_RESOURCE_URL = 'V1/customer/me/';
    /**#@-*/

    const CONTENT_TYPE_JSON = 'application/json';
    const CONTENT_TYPE_XML = 'application/xml';

    /** @var CustomerHelper */
    private $customerHelper;

    /** @var array */
    private $customerData;

    /** @var string */
    protected $defaultStoreCode;

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
        $this->defaultStoreCode = Bootstrap::getObjectManager()
            ->get('Magento\Store\Model\StoreManagerInterface')
            ->getStore()
            ->getCode();
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

    public function testApiWithSelfPermissions()
    {
        $arguments = ["username" => $this->customerData['email'], "password" => CustomerHelper::PASSWORD];
        $session = $this->login(self::LOGIN_REGISTERED, $arguments, self::CONTENT_TYPE_JSON);
        $header = ['Cookie: ' . $session, 'Accept: application/json'];
        $this->curlClient->setRestBasePath('/rest/');
        $response = $this->curlClient
            ->get('/' . $this->defaultStoreCode . '/' . self::CUSTOMER_RESOURCE_URL, null, $header);
        $this->assertNotNull($this->customerData['email'], $response['email']);
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
        $responseHeader = $this->curlClient->getCurrentResponseArray()['header'];
        //Check for Set-Cookie to make sure correct header will be set
        $this->assertContains("Set-Cookie: frontend=", $responseHeader);
        preg_match_all('/frontend=.*?;/', $responseHeader, $matches);
        //The curl client here returns two session ids erroneously in the response header. The second one is correct
        return $matches[0][1];
    }
}

