<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Service\V1;

use Magento\Customer\Service\V1\CustomerAccountService;
use Magento\Framework\Exception\InputException;
use Magento\Integration\Model\Oauth\Token as TokenModel;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\User\Model\User as UserModel;
use Magento\Webapi\Exception as HTTPExceptionCodes;

/**
 * api-functional test for \Magento\Integration\Service\V1\TokenService.
 */
class TokenServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = "integrationTokenServiceV1";
    const SERVICE_VERSION = "V1";
    const RESOURCE_PATH_CUSTOMER_TOKEN = "/V1/integration/customer/token";
    const RESOURCE_PATH_ADMIN_TOKEN = "/V1/integration/admin/token";

    /**
     * @var TokenServiceInterface
     */
    private $tokenService;

    /**
     * @var CustomerAccountService
     */
    private $customerAccountService;

    /**
     * @var TokenModel
     */
    private $tokenModel;

    /**
     * @var UserModel
     */
    private $userModel;

    /**
     * Setup TokenService
     */
    public function setUp()
    {
        $this->_markTestAsRestOnly();
        $this->tokenService = Bootstrap::getObjectManager()->get('Magento\Integration\Service\V1\TokenService');
        $this->customerAccountService = Bootstrap::getObjectManager()->get(
            'Magento\Customer\Service\V1\CustomerAccountService'
        );
        $this->tokenModel = Bootstrap::getObjectManager()->get('Magento\Integration\Model\Oauth\Token');
        $this->userModel = Bootstrap::getObjectManager()->get('Magento\User\Model\User');
    }

    /**
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     */
    public function testCreateCustomerAccessToken()
    {
        $customerUserName = 'customer@example.com';
        $password = 'password';

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH_CUSTOMER_TOKEN,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ]
        ];
        $requestData = ['username' => $customerUserName, 'password' => $password];
        $accessToken = $this->_webApiCall($serviceInfo, $requestData);

        $customerData = $this->customerAccountService->authenticate($customerUserName, $password);
        /** @var $token TokenModel */
        $token = $this->tokenModel->loadByCustomerId($customerData->getId())->getToken();
        $this->assertEquals($accessToken, $token);
    }

    /**
     * @dataProvider validationDataProvider
     */
    public function testCreateCustomerAccessTokenEmptyOrNullCredentials($username, $password)
    {
        try {
            $serviceInfo = [
                'rest' => [
                    'resourcePath' => self::RESOURCE_PATH_CUSTOMER_TOKEN,
                    'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
                ]
            ];
            $requestData = ['username' => '', 'password' => ''];
            $this->_webApiCall($serviceInfo, $requestData);
        } catch (\Exception $e) {
            $this->assertInputExceptionMessages($e);
        }
    }

    public function testCreateCustomerAccessTokenInvalidCustomer()
    {
        $customerUserName = 'invalid';
        $password = 'invalid';
        try {
            $serviceInfo = [
                'rest' => [
                    'resourcePath' => self::RESOURCE_PATH_CUSTOMER_TOKEN,
                    'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
                ]
            ];
            $requestData = ['username' => $customerUserName, 'password' => $password];
            $this->_webApiCall($serviceInfo, $requestData);
        } catch (\Exception $e) {
            $this->assertEquals(HTTPExceptionCodes::HTTP_UNAUTHORIZED, $e->getCode());
            $exceptionData = $this->processRestExceptionResult($e);
            $expectedExceptionData = ['message' => 'Invalid login or password.'];
        }
        $this->assertEquals($expectedExceptionData, $exceptionData);
    }

    /**
     * @magentoApiDataFixture Magento/User/_files/user_with_role.php
     */
    public function testCreateAdminAccessToken()
    {
        $adminUserNameFromFixture = 'adminUser';

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH_ADMIN_TOKEN,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ]
        ];
        $requestData = [
            'username' => $adminUserNameFromFixture,
            'password' => \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
        ];
        $accessToken = $this->_webApiCall($serviceInfo, $requestData);

        $adminUserId = $this->userModel->loadByUsername($adminUserNameFromFixture)->getId();
        /** @var $token TokenModel */
        $token = $this->tokenModel
            ->loadByAdminId($adminUserId)
            ->getToken();
        $this->assertEquals($accessToken, $token);
    }

    /**
     * @dataProvider validationDataProvider
     */
    public function testCreateAdminAccessTokenEmptyOrNullCredentials()
    {
        try {
            $serviceInfo = [
                'rest' => [
                    'resourcePath' => self::RESOURCE_PATH_ADMIN_TOKEN,
                    'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
                ]
            ];
            $requestData = ['username' => '', 'password' => ''];
            $this->_webApiCall($serviceInfo, $requestData);
        } catch (\Exception $e) {
            $this->assertInputExceptionMessages($e);
        }
    }

    public function testCreateAdminAccessTokenInvalidCustomer()
    {
        $customerUserName = 'invalid';
        $password = 'invalid';
        try {
            $serviceInfo = [
                'rest' => [
                    'resourcePath' => self::RESOURCE_PATH_CUSTOMER_TOKEN,
                    'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
                ]
            ];
            $requestData = ['username' => $customerUserName, 'password' => $password];
            $this->_webApiCall($serviceInfo, $requestData);
        } catch (\Exception $e) {
            $this->assertEquals(HTTPExceptionCodes::HTTP_UNAUTHORIZED, $e->getCode());
            $exceptionData = $this->processRestExceptionResult($e);
            $expectedExceptionData = ['message' => 'Invalid login or password.'];
        }
        $this->assertEquals($expectedExceptionData, $exceptionData);
    }

    /**
     * Provider to test input validation
     *
     * @return array
     */
    public function validationDataProvider()
    {
        return [
            'Check for empty credentials' => ['', ''],
            'Check for null credentials' => [null, null]
        ];
    }

    /**
     * Assert for presence of Input exception messages
     *
     * @param \Exception $e
     */
    private function assertInputExceptionMessages($e)
    {
        $this->assertEquals(HTTPExceptionCodes::HTTP_BAD_REQUEST, $e->getCode());
        $exceptionData = $this->processRestExceptionResult($e);
        $expectedExceptionData = [
            'message' => InputException::DEFAULT_MESSAGE,
            'errors' => [
                [
                    'message' => InputException::REQUIRED_FIELD,
                    'parameters' => [
                        'fieldName' => 'username',
                    ]
                ],
                [
                    'message' => InputException::REQUIRED_FIELD,
                    'parameters' => [
                        'fieldName' => 'password',
                    ]
                ]
            ]
        ];
        $this->assertEquals($expectedExceptionData, $exceptionData);
    }
}
 
