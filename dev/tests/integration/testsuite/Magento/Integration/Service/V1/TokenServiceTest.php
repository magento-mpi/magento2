<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Service\V1;

use Magento\Customer\Service\V1\CustomerAccountService;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Integration\Model\Oauth\Token as TokenModel;
use Magento\User\Model\User as UserModel;

/**
 * Test class for \Magento\Integration\Service\V1\TokenService.
 *
 * @magentoDbIsolation enabled
 */
class TokenServiceTest extends \PHPUnit_Framework_TestCase
{
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
        $this->tokenService = Bootstrap::getObjectManager()->get('Magento\Integration\Service\V1\TokenService');
        $this->customerAccountService = Bootstrap::getObjectManager()->get(
            'Magento\Customer\Service\V1\CustomerAccountService'
        );
        $this->tokenModel = Bootstrap::getObjectManager()->get('Magento\Integration\Model\Oauth\Token');
        $this->userModel = Bootstrap::getObjectManager()->get('Magento\User\Model\User');
    }

    /**
     * @magento
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testCreateCustomerAccessToken()
    {
        $customerUserName = 'customer@example.com';
        $password = 'password';
        $accessToken = $this->tokenService->createCustomerAccessToken($customerUserName, $password);
        $customerData = $this->customerAccountService->authenticate($customerUserName, $password);
        /** @var $token TokenModel */
        $token = $this->tokenModel->loadByCustomerId($customerData->getId())->getToken();
        $this->assertEquals($accessToken, $token);
    }

    public function testCreateAdminAccessToken()
    {
        $defaultAdminUser = 'User';
        //Using integration tests fixtures' default admin username and password
        $accessToken = $this->tokenService->createAdminAccessToken(
            $defaultAdminUser,
            \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
        );
        $adminUserId = $this->userModel->loadByUsername($defaultAdminUser)->getId();
        /** @var $token TokenModel */
        $token = $this->tokenModel
            ->loadByAdminId($adminUserId)
            ->getToken();
        $this->assertEquals($accessToken, $token);
    }
}
 
