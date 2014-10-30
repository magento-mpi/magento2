<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1;

use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerDataBuilder;
use Magento\Customer\Service\V1\Data\CustomerDetailsBuilder;
use Magento\Integration\Model\Oauth\Token as TokenModel;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Helper\Customer as CustomerHelper;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

/**
 * Class CustomerAccountServiceMeTest
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @magentoApiDataFixture Magento/Customer/_files/customer.php
 */
class CustomerAccountServiceMeTest extends WebapiAbstract
{
    const RESOURCE_PATH = '/V1/customers/me';
    const RESOURCE_PATH_CUSTOMER_TOKEN = "/V1/integration/customer/token";

    /** @var CustomerAccountServiceInterface */
    private $customerAccountService;

    /** @var CustomerDataBuilder */
    private $customerBuilder;

    /** @var CustomerDetailsBuilder */
    private $customerDetailsBuilder;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /** @var CustomerHelper */
    private $customerHelper;

    /** @var $token TokenModel */
    private $token;

    private $customerData;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $this->_markTestAsRestOnly();

        $this->customerRegistry = Bootstrap::getObjectManager()->get(
            'Magento\Customer\Model\CustomerRegistry'
        );

        $this->customerAccountService = Bootstrap::getObjectManager()->get(
            'Magento\Customer\Service\V1\CustomerAccountServiceInterface',
            ['customerRegistry' => $this->customerRegistry]
        );
        $this->customerBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Api\Data\CustomerDataBuilder'
        );
        $this->customerDetailsBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\Data\CustomerDetailsBuilder'
        );
        $this->customerHelper = new CustomerHelper();
        $this->customerData = $this->customerHelper->createSampleCustomer();

        // get token
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH_CUSTOMER_TOKEN,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ]
        ];
        $requestData = [
            'username' => $this->customerData[\Magento\Customer\Model\Data\Customer::EMAIL],
            'password' => 'test@123'
        ];
        $this->token = $this->_webApiCall($serviceInfo, $requestData);
    }

    public function tearDown()
    {
        unset($this->customerAccountService);
    }

    public function testChangePassword()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/password',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT,
                'token' => $this->token
            ]
        ];
        $requestData = ['currentPassword' => 'test@123', 'newPassword' => '123@test'];
        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/customers/login',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ]
        ];
        $requestData = [
            'username' => $this->customerData[\Magento\Customer\Model\Data\Customer::EMAIL],
            'password' => '123@test'
        ];
        $customerResponseData = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(
            $this->customerData[\Magento\Customer\Model\Data\Customer::ID],
            $customerResponseData[\Magento\Customer\Model\Data\Customer::ID]
        );
    }

    public function testUpdateCustomer()
    {
        $customerDetails = $this->_getCustomerDetails($this->customerData[\Magento\Customer\Model\Data\Customer::ID]);
        $lastName = $customerDetails->getCustomer()->getLastname();

        $updatedCustomer = $this->customerBuilder
            ->populate($customerDetails->getCustomer())
            ->setLastname($lastName . "Updated")
            ->create();

        $updatedCustomerDetails = $this->customerDetailsBuilder
            ->populate($customerDetails)
            ->setCustomer($updatedCustomer)
            ->setAddresses($customerDetails->getAddresses())
            ->create();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => RestConfig::HTTP_METHOD_PUT,
                'token' => $this->token
            ]
        ];
        $customerDetailsAsArray = $updatedCustomerDetails->__toArray();
        $requestData = ['customerDetails' => $customerDetailsAsArray];
        $response = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue($response);

        $customerDetails = $this->_getCustomerDetails($this->customerData[\Magento\Customer\Model\Data\Customer::ID]);
        $this->assertEquals($lastName . "Updated", $customerDetails->getCustomer()->getLastname());
    }

    public function testGetCustomerDetails()
    {
        //Get expected details from the Service directly
        $expectedCustomerDetails = $this
            ->_getCustomerDetails($this->customerData[\Magento\Customer\Model\Data\Customer::ID])
            ->__toArray();
        $expectedCustomerDetails['addresses'][0]['id'] =
            (int)$expectedCustomerDetails['addresses'][0]['id'];

        $expectedCustomerDetails['addresses'][1]['id'] =
            (int)$expectedCustomerDetails['addresses'][1]['id'];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
                'token' => $this->token
            ]
        ];
        $customerDetailsResponse = $this->_webApiCall($serviceInfo);

        // TODO: Reset custom_attributes to empty array for now since webapi does not support it. Need to fix this.
        unset($expectedCustomerDetails['customer']['custom_attributes']);
        unset($customerDetailsResponse['customer']['custom_attributes']); //for REST

        $this->assertEquals($expectedCustomerDetails, $customerDetailsResponse);
    }

    public function testGetCustomerActivateCustomer()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/activate',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT,
                'token' => $this->token
            ]
        ];
        $requestData = ['confirmationKey' => $this->customerData[\Magento\Customer\Model\Data\Customer::CONFIRMATION]];
        $customerResponseData = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(
            $this->customerData[\Magento\Customer\Model\Data\Customer::ID],
            $customerResponseData[\Magento\Customer\Model\Data\Customer::ID]
        );
        // Confirmation key is removed after confirmation
        $this->assertFalse(isset($customerResponseData[\Magento\Customer\Model\Data\Customer::CONFIRMATION]));
    }

    /**
     * Return the customer details.
     *
     * @param int $customerId
     * @return \Magento\Customer\Service\V1\Data\CustomerDetails
     */
    protected function _getCustomerDetails($customerId)
    {
        $details = $this->customerAccountService->getCustomerDetails($customerId);
        $this->customerRegistry->remove($customerId);
        return $details;
    }
}
