<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi;

use Magento\Exception\NoSuchEntityException;
use Magento\Webapi\Exception as HTTPExceptionCodes;
use Magento\Customer\Service\V1\Data\Customer;
use Magento\Customer\Service\V1\Data\CustomerDetails;
use Magento\Customer\Service\V1\Data\CustomerDetailsBuilder;
use Magento\Customer\Service\V1\Data\CustomerBuilder;
use Magento\Customer\Service\V1\Data\AddressBuilder;
use Magento\Customer\Service\V1\Data\RegionBuilder;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Exception\InputException;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

/**
 * Class CustomerAccountServiceTest
 */
class CustomerAccountServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'customerCustomerAccountServiceV1';

    const SERVICE_VERSION = 'V1';

    const RESOURCE_PATH = '/V1/customerAccounts';

    const CONFIRMATION = 'a4fg7h893e39d';

    const CREATED_AT = '2013-11-05';

    const CREATED_IN = 'default';

    const STORE_NAME = 'Store Name';

    const DOB = '1970-01-01';

    const GENDER = 'Male';

    const GROUP_ID = 1;

    const MIDDLENAME = 'A';

    const PREFIX = 'Mr.';

    const STORE_ID = 1;

    const SUFFIX = 'Esq.';

    const TAXVAT = '12';

    const WEBSITE_ID = 1;

    /** Sample values for testing */
    const ID = 1;

    const FIRSTNAME = 'Jane';

    const LASTNAME = 'Doe';

    const NAME = 'J';

    const ATTRIBUTE_CODE = 'attribute_code';

    const ATTRIBUTE_VALUE = 'attribute_value';

    /** @var CustomerAccountServiceInterface */
    private $customerAccountService;

    /** @var AddressBuilder */
    private $addressBuilder;

    /** @var CustomerBuilder */
    private $customerBuilder;

    /** @var CustomerDetailsBuilder */
    private $customerDetailsBuilder;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $this->_markTestAsRestOnly();

        $this->customerAccountService = Bootstrap::getObjectManager()->get(
            'Magento\Customer\Service\V1\CustomerAccountServiceInterface'
        );
        $this->addressBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\Data\AddressBuilder'
        );
        $this->customerBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\Data\CustomerBuilder'
        );
        $this->customerDetailsBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\Data\CustomerDetailsBuilder'
        );
    }

    /**
     * Execute per test cleanup.
     */
    public function tearDown()
    {
        unset($this->customerAccountService);
    }

    public function testCreateCustomer()
    {
        $customerData = $this->_createSampleCustomer();
        $this->assertNotNull($customerData['id']);
    }

    public function testCreateCustomerWithErrors()
    {
        $serviceInfo = array(
            'rest' => array('resourcePath' => self::RESOURCE_PATH, 'httpMethod' => RestConfig::HTTP_METHOD_POST)
        );
        $customerDetailsAsArray = $this->_createSampleCustomerDetailsData()->__toArray();
        unset($customerDetailsAsArray['customer']['firstname']);
        unset($customerDetailsAsArray['customer']['email']);
        $requestData = array('customerDetails' => $customerDetailsAsArray, 'password' => 'test@123');
        try{
            $this->_webApiCall($serviceInfo, $requestData);
            $this->fail('Expected exception did not occur.');
        } catch (\Exception $e) {
            $this->assertEquals(400, $e->getCode());
            $exceptionData = $this->_processRestExceptionResult($e);
            $expectedExceptionData = [
                    'message' => InputException::DEFAULT_MESSAGE,
                    'errors' => [
                        [
                            'message' => InputException::REQUIRED_FIELD,
                            'parameters' => [
                                'fieldName' => 'firstname',
                            ]
                        ],
                        [
                            'message' => InputException::INVALID_FIELD_VALUE,
                            'parameters' => [
                                'fieldName' => 'email',
                                'value' => ''
                            ]
                        ]
                    ]
            ];
            $this->assertEquals($expectedExceptionData, $exceptionData);
        }
    }

    public function testGetCustomerDetails()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData['id'],
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            )
        );
        $customerDetailsData = $this->_webApiCall($serviceInfo);
        $this->assertNotNull($customerData, $customerDetailsData['customer']);
    }

    public function testGetCustomer()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::ID] . '/customer',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            )
        );
        $customerResponseData = $this->_webApiCall($serviceInfo);
        $this->assertNotNull($customerData, $customerResponseData);
    }

    public function testGetCustomerActivateCustomer()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::ID] . '/activateCustomer',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            )
        );
        $requestData = array('confirmationKey' => $customerData[Customer::CONFIRMATION]);
        $customerResponseData = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($customerData[Customer::ID], $customerResponseData[Customer::ID]);
        // Confirmation key is removed after confirmation
        $this->assertFalse(isset($customerResponseData[Customer::CONFIRMATION]));
    }

    public function testAuthenticateCustomer()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . '/authenticate',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            )
        );
        $requestData = array('username' => $customerData[Customer::EMAIL], 'password' => 'test@123');
        $customerResponseData = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($customerData[Customer::ID], $customerResponseData[Customer::ID]);
    }

    public function testChangePassword()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::ID] . '/changePassword',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            )
        );
        $requestData = array('currentPassword' => 'test@123', 'newPassword' => '123@test');
        $this->_webApiCall($serviceInfo, $requestData);

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . '/authenticate',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            )
        );
        $requestData = array('username' => $customerData[Customer::EMAIL], 'password' => '123@test');
        $customerResponseData = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($customerData[Customer::ID], $customerResponseData[Customer::ID]);
    }

    public function testValidateResetPasswordLinkToken()
    {
        $customerData = $this->_createSampleCustomer();
        /** @var \Magento\Customer\Model\Customer $customerModel */
        $customerModel = Bootstrap::getObjectManager()->create('Magento\Customer\Model\CustomerFactory')->create();
        $customerModel->load($customerData[Customer::ID]);
        $rpToken = 'lsdj579slkj5987slkj595lkj';
        $customerModel->setRpToken('lsdj579slkj5987slkj595lkj');
        $customerModel->setRpTokenCreatedAt(date('Y-m-d'));
        $customerModel->save();
        $path = self::RESOURCE_PATH .
            '/' .
            $customerData[Customer::ID] .
            '/validateResetPasswordLinkToken/' .
            $rpToken;
        $serviceInfo = array('rest' => array('resourcePath' => $path, 'httpMethod' => RestConfig::HTTP_METHOD_GET));
        $this->_webApiCall($serviceInfo);
    }

    public function testValidateResetPasswordLinkTokenInvalidToken()
    {
        $customerData = $this->_createSampleCustomer();
        $path = self::RESOURCE_PATH . '/' . $customerData[Customer::ID] . '/validateResetPasswordLinkToken/invalid';
        $serviceInfo = array('rest' => array('resourcePath' => $path, 'httpMethod' => RestConfig::HTTP_METHOD_GET));
        try {
            $this->_webApiCall($serviceInfo);
        } catch (\Exception $e) {
            $errorObj = $this->_processRestExceptionResult($e);
            $this->assertEquals("Reset password token mismatch.", $errorObj['message']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_BAD_REQUEST, $e->getCode());
        }
    }

    public function testInitiatePasswordReset()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . '/initiatePasswordReset',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            )
        );
        $requestData = array(
            'email' => $customerData[Customer::EMAIL],
            'websiteId' => $customerData[Customer::WEBSITE_ID],
            'template' => CustomerAccountServiceInterface::EMAIL_RESET
        );
        // This api doesn't return any response.
        // No exception or response means the request was processed successfully.
        // The webapi framework does not return the header information as yet. A check for HTTP 200 would be ideal here
        $this->_webApiCall($serviceInfo, $requestData);
    }

    public function testSendPasswordResetLinkBadEmailOrWebsite()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . '/initiatePasswordReset',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            )
        );
        $requestData = array(
            'email' => 'dummy@example.com',
            'websiteId' => 0,
            'template' => CustomerAccountServiceInterface::EMAIL_RESET
        );
        try {
            $this->_webApiCall($serviceInfo, $requestData);
        } catch (\Exception $e) {
            $errorObj = $this->_processRestExceptionResult($e);
            $this->assertEquals(
                NoSuchEntityException::MESSAGE_DOUBLE_FIELDS,
                $errorObj['message']
            );
            $this->assertEquals(
                [
                    'fieldName' => 'email',
                    'fieldValue' => 'dummy@example.com',
                    'field2Name' => 'websiteId',
                    'field2Value'=> '0'
                ],
                $errorObj['parameters']
            );
            $this->assertEquals(HTTPExceptionCodes::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    public function testGetConfirmationStatus()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::ID] . '/confirmationStatus',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            )
        );
        $confirmationResponse = $this->_webApiCall($serviceInfo);
        $this->assertEquals(CustomerAccountServiceInterface::ACCOUNT_CONFIRMATION_NOT_REQUIRED, $confirmationResponse);
    }

    public function testResendConfirmation()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . '/resendConfirmation',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            )
        );
        $requestData = array(
            'email' => $customerData[Customer::EMAIL],
            'websiteId' => $customerData[Customer::WEBSITE_ID]
        );
        // This api doesn't return any response.
        // No exception or response means the request was processed successfully.
        // The webapi framework does not return the header information as yet. A check for HTTP 200 would be ideal here
        $this->_webApiCall($serviceInfo, $requestData);
    }

    public function testResendConfirmationBadEmailOrWebsite()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . '/resendConfirmation',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            )
        );
        $requestData = array('email' => 'dummy@example.com', 'websiteId' => 0);
        try {
            $this->_webApiCall($serviceInfo, $requestData);
        } catch (\Exception $e) {
            $errorObj = $this->_processRestExceptionResult($e);
            $this->assertEquals(
                NoSuchEntityException::MESSAGE_DOUBLE_FIELDS, $errorObj['message']);
            $this->assertEquals(
                [
                    'fieldName' => 'email',
                    'fieldValue' => 'dummy@example.com',
                    'field2Name' => 'websiteId',
                    'field2Value' => '0'
                ],
                $errorObj['parameters']
            );
            $this->assertEquals(HTTPExceptionCodes::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    public function testValidateCustomerData()
    {
        $customerData = $this->_createSampleCustomerDataObject();
        $customerData = $this->customerBuilder->populate(
            $customerData
        )->setFirstname(
            null
        )->setLastname(
            null
        )->create();

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . '/validateCustomerData',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            )
        );
        $requestData = array('customer' => $customerData->__toArray(), 'attributes' => array());
        $validationResponse = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(-1, $validationResponse[0]);
        $this->assertEquals('The first name cannot be empty., The last name cannot be empty.', $validationResponse[1]);
    }

    public function testCanModify()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::ID] . '/canModify',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            )
        );
        $this->assertTrue($this->_webApiCall($serviceInfo));
    }

    public function testCanDelete()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::ID] . '/canDelete',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            )
        );
        $this->assertTrue($this->_webApiCall($serviceInfo));
    }

    public function testDeleteCustomer()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::ID],
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            )
        );
        $this->_webApiCall($serviceInfo);

        //Verify if the customer is deleted
        $this->setExpectedException(
            'Magento\Exception\NoSuchEntityException',
            sprintf("No such entity with customerId = %s", $customerData[Customer::ID])
        );
        $this->customerAccountService->getCustomerDetails($customerData[Customer::ID]);
    }

    public function testDeleteCustomerInvalidCustomerId()
    {
        $invalidId = -1;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . '/' . $invalidId,
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            )
        );
        try {
            $this->_webApiCall($serviceInfo);
        } catch (\Exception $e) {
            $errorObj = $this->_processRestExceptionResult($e);
            $this->assertEquals(NoSuchEntityException::MESSAGE_SINGLE_FIELD, $errorObj['message']);
            $this->assertEquals(['fieldName' => 'customerId', 'fieldValue' => '-1'], $errorObj['parameters']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    public function testEmailAvailable()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . '/isEmailAvailable',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            )
        );
        $requestData = array(
            'customerEmail' => $customerData[Customer::EMAIL],
            'websiteId' => $customerData[Customer::WEBSITE_ID]
        );
        $this->assertFalse($this->_webApiCall($serviceInfo, $requestData));
    }

    public function testEmailAvailableInvalidEmail()
    {
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . '/isEmailAvailable',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            )
        );
        $requestData = array('customerEmail' => 'invalid', 'websiteId' => 0);
        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));
    }

    public function testUpdateCustomer()
    {
        $customerData = $this->_createSampleCustomer();
        $customerDetails = $this->customerAccountService->getCustomerDetails($customerData[Customer::ID]);
        $lastName = $customerDetails->getCustomer()->getLastname();

        $updatedCustomer = $this->customerBuilder->populate(
            $customerDetails->getCustomer()
        )->setLastname(
            $lastName . "Updated"
        )->create();

        $updatedCustomerDetails = $this->customerDetailsBuilder->populate(
            $customerDetails
        )->setCustomer(
            $updatedCustomer
        )->setAddresses(
            $customerDetails->getAddresses()
        )->create();


        $serviceInfo = array(
            'rest' => array('resourcePath' => self::RESOURCE_PATH, 'httpMethod' => RestConfig::HTTP_METHOD_PUT)
        );
        $customerDetailsAsArray = $updatedCustomerDetails->__toArray();
        $requestData = array('customerDetails' => $customerDetailsAsArray);
        $this->_webApiCall($serviceInfo, $requestData);

        //Verify if the customer is updated
        $customerDetails = $this->customerAccountService->getCustomerDetails($customerData[Customer::ID]);
        $this->assertEquals($lastName . "Updated", $customerDetails->getCustomer()->getLastname());
    }

    public function testUpdateCustomerException()
    {
        $customerData = $this->_createSampleCustomer();
        $customerDetails = $this->customerAccountService->getCustomerDetails($customerData[Customer::ID]);
        $lastName = $customerDetails->getCustomer()->getLastname();

        //Set non-existent id
        $updatedCustomer = $this->customerBuilder->populate(
            $customerDetails->getCustomer()
        )->setLastname(
            $lastName . "Updated"
        )->setId(
            -1
        )->create();

        $updatedCustomerDetails = $this->customerDetailsBuilder->populate(
            $customerDetails
        )->setCustomer(
            $updatedCustomer
        )->setAddresses(
            $customerDetails->getAddresses()
        )->create();


        $serviceInfo = array(
            'rest' => array('resourcePath' => self::RESOURCE_PATH, 'httpMethod' => RestConfig::HTTP_METHOD_PUT)
        );
        $customerDetailsAsArray = $updatedCustomerDetails->__toArray();
        $requestData = array('customerDetails' => $customerDetailsAsArray);
        try {
            $this->_webApiCall($serviceInfo, $requestData);
        } catch (\Exception $e) {
            $errorObj = $this->_processRestExceptionResult($e);
            $this->assertEquals(NoSuchEntityException::MESSAGE_SINGLE_FIELD, $errorObj['message']);
            $this->assertEquals(['fieldName' => 'customerId', 'fieldValue' => '-1'], $errorObj['parameters']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    /**
     * @return CustomerDetails
     */
    private function _createSampleCustomerDetailsData()
    {
        $this->addressBuilder->setCountryId(
            'US'
        )->setDefaultBilling(
            true
        )->setDefaultShipping(
            true
        )->setPostcode(
            '75477'
        )->setRegion(
            (new RegionBuilder())->populateWithArray(
                array('region_code' => 'AL', 'region' => 'Alabama', 'region_id' => 1)
            )->create()
        )->setStreet(
            array('Green str, 67')
        )->setTelephone(
            '3468676'
        )->setCity(
            'CityM'
        )->setFirstname(
            'John'
        )->setLastname(
            'Smith'
        );
        $address1 = $this->addressBuilder->create();

        $this->addressBuilder->setCountryId(
            'US'
        )->setDefaultBilling(
            false
        )->setDefaultShipping(
            false
        )->setPostcode(
            '47676'
        )->setRegion(
            (new RegionBuilder())->populateWithArray(
                array('region_code' => 'AL', 'region' => 'Alabama', 'region_id' => 1)
            )->create()
        )->setStreet(
            array('Black str, 48')
        )->setCity(
            'CityX'
        )->setTelephone(
            '3234676'
        )->setFirstname(
            'John'
        )->setLastname(
            'Smith'
        );

        $address2 = $this->addressBuilder->create();

        $customerData = $this->_createSampleCustomerDataObject();
        $customerDetails = $this->customerDetailsBuilder->setAddresses(
            array($address1, $address2)
        )->setCustomer(
            $customerData
        )->create();
        return $customerDetails;
    }

    /**
     * Create customer using setters.
     *
     * @return Customer
     */
    private function _createSampleCustomerDataObject()
    {
        $customerData = array(
            Customer::FIRSTNAME => self::FIRSTNAME,
            Customer::LASTNAME => self::LASTNAME,
            Customer::EMAIL => 'janedoe' . md5(rand()) . '@example.com',
            Customer::CONFIRMATION => self::CONFIRMATION,
            Customer::CREATED_AT => self::CREATED_AT,
            Customer::CREATED_IN => self::STORE_NAME,
            Customer::DOB => self::DOB,
            Customer::GENDER => self::GENDER,
            Customer::GROUP_ID => self::GROUP_ID,
            Customer::MIDDLENAME => self::MIDDLENAME,
            Customer::PREFIX => self::PREFIX,
            Customer::STORE_ID => self::STORE_ID,
            Customer::SUFFIX => self::SUFFIX,
            Customer::TAXVAT => self::TAXVAT,
            Customer::WEBSITE_ID => self::WEBSITE_ID
        );
        return $this->customerBuilder->populateWithArray($customerData)->create();
    }

    /**
     * @return array
     */
    protected function _createSampleCustomer()
    {
        $serviceInfo = array(
            'rest' => array('resourcePath' => self::RESOURCE_PATH, 'httpMethod' => RestConfig::HTTP_METHOD_POST)
        );
        $customerDetailsAsArray = $this->_createSampleCustomerDetailsData()->__toArray();
        $requestData = array('customerDetails' => $customerDetailsAsArray, 'password' => 'test@123');
        $customerData = $this->_webApiCall($serviceInfo, $requestData);
        return $customerData;
    }

    /**
     * @param \Exception $e
     * @return array
     * <pre> ex.
     * 'message' => "No such entity with %fieldName1 = %value1, %fieldName2 = %value2"
     * 'parameters' => [
     *      "fieldName1" => "email",
     *      "value1" => "dummy@example.com",
     *      "fieldName2" => "websiteId",
     *      "value2" => 0
     * ]
     *
     * </pre>
     */
    protected function _processRestExceptionResult(\Exception $e)
    {
        $error = json_decode($e->getMessage(), true);
        //Remove line breaks and replace with space
        $error['message'] = trim(preg_replace('/\s+/', ' ', $error['message']));
        // remove trace and type, will only be present if server is in dev mode
        unset($error['trace']);
        unset($error['type']);
        return $error;
    }
}
