<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1;

use Magento\Customer\Service\V1\Data\AddressBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Service\V1\Data\FilterBuilder;
use Magento\Framework\Service\V1\Data\SearchCriteria;
use Magento\Webapi\Exception as HTTPExceptionCodes;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Service\V1\Data\Customer;
use Magento\Customer\Service\V1\Data\CustomerBuilder;
use Magento\Customer\Service\V1\Data\CustomerDetails;
use Magento\Customer\Service\V1\Data\CustomerDetailsBuilder;
use Magento\Customer\Service\V1\Data\RegionBuilder;
use Magento\Framework\Service\V1\Data\Search\FilterGroupBuilder;
use Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\Framework\Exception\InputException;

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

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /** @var FilterGroupBuilder */
    private $filterGroupBuilder;

    /** @var \Magento\Webapi\Helper\Data */
    private $helper;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $this->customerRegistry = Bootstrap::getObjectManager()->get(
            'Magento\Customer\Model\CustomerRegistry'
        );

        $this->customerAccountService = Bootstrap::getObjectManager()->get(
            'Magento\Customer\Service\V1\CustomerAccountServiceInterface',
            [ 'customerRegistry' => $this->customerRegistry ]
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
        $this->searchCriteriaBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Service\V1\Data\SearchCriteriaBuilder'
        );
        $this->filterGroupBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Service\V1\Data\Search\FilterGroupBuilder'
        );
        $this->helper = Bootstrap::getObjectManager()->create('Magento\Webapi\Helper\Data');
    }

    public function tearDown()
    {
        unset($this->customerAccountService);
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Attribute');
        $model->load('address_user_attribute', 'attribute_code')
            ->delete();
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Attribute');
        $model->load('user_attribute', 'attribute_code')
            ->delete();
    }

    public function testCreateCustomer()
    {
        $customerData = $this->_createSampleCustomer();
        $this->assertNotNull($customerData['id']);
    }

    public function testCreateCustomerWithErrors()
    {
        $serviceInfo = [
            'rest' => ['resourcePath' => self::RESOURCE_PATH, 'httpMethod' => RestConfig::HTTP_METHOD_POST],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'CreateCustomer'
            ]
        ];

        $customerDetailsAsArray = $this->_createSampleCustomerDetailsData()->__toArray();
        unset($customerDetailsAsArray['customer']['firstname']);
        unset($customerDetailsAsArray['customer']['email']);
        $requestData = ['customerDetails' => $customerDetailsAsArray, 'password' => 'test@123'];
        try {
            $this->_webApiCall($serviceInfo, $requestData);
            $this->fail('Expected exception did not occur.');
        } catch (\Exception $e) {
            if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
                $this->assertInstanceOf('SoapFault', $e);
                $exceptionData = $e->getMessage();
                $expectedExceptionData = "SOAP-ERROR: Encoding: object has no 'email' property";
            } else {
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
            }
            $this->assertEquals($expectedExceptionData, $exceptionData);
        }
    }

    public function testGetCustomerDetails()
    {
        //Create a customer
        $customerData = $this->_createSampleCustomer();

        //Get expected details from the Service directly
        $expectedCustomerDetails = $this->_getCustomerDetails($customerData['id'])->__toArray();

        //Test GetDetails
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData['id'],
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetCustomerDetails'
            ]
        ];
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $customerDetailsResponse = $this->_webApiCall($serviceInfo, ['customerId' => $customerData['id']]);
        } else {
            $customerDetailsResponse = $this->_webApiCall($serviceInfo);
        }
        // TODO: Reset custom_attributes to empty array for now since webapi does not support it. Need to fix this.
        unset($expectedCustomerDetails['customer']['custom_attributes']);
        unset($customerDetailsResponse['customer']['customAttributes']); //For SOAP
        unset($customerDetailsResponse['customer']['custom_attributes']); //for REST

        $this->assertEquals($expectedCustomerDetails, $customerDetailsResponse);
    }

    public function testGetCustomer()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::ID] . '/customer',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetCustomer'
            ]
        ];
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $customerResponseData = $this->_webApiCall($serviceInfo, ['customerId' => $customerData['id']]);
        } else {
            $customerResponseData = $this->_webApiCall($serviceInfo);
        }
        $this->assertEquals($customerData, $customerResponseData);
    }

    public function testGetCustomerActivateCustomer()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::ID] . '/activateCustomer',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'ActivateCustomer'
            ]
        ];
        $requestData = ['confirmationKey' => $customerData[Customer::CONFIRMATION]];
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $requestData['customerId'] = $customerData['id'];
            $customerResponseData = $this->_webApiCall($serviceInfo, $requestData);
        } else {
            $customerResponseData = $this->_webApiCall($serviceInfo, $requestData);
        }
        $this->assertEquals($customerData[Customer::ID], $customerResponseData[Customer::ID]);
        // Confirmation key is removed after confirmation
        $this->assertFalse(isset($customerResponseData[Customer::CONFIRMATION]));
    }

    public function testAuthenticateCustomer()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/authenticate',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Authenticate'
            ]
        ];
        $requestData = ['username' => $customerData[Customer::EMAIL], 'password' => 'test@123'];
        $customerResponseData = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($customerData[Customer::ID], $customerResponseData[Customer::ID]);
    }

    public function testChangePassword()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::ID] . '/changePassword',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'ChangePassword'
            ]
        ];
        $requestData = ['currentPassword' => 'test@123', 'newPassword' => '123@test'];
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $requestData['customerId'] = $customerData['id'];
        }
        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/authenticate',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Authenticate'
            ]
        ];
        $requestData = ['username' => $customerData[Customer::EMAIL], 'password' => '123@test'];
        $customerResponseData = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($customerData[Customer::ID], $customerResponseData[Customer::ID]);
    }

    public function testValidateResetPasswordLinkToken()
    {
        $customerData = $this->_createSampleCustomer();
        /** @var \Magento\Customer\Model\Customer $customerModel */
        $customerModel = Bootstrap::getObjectManager()->create('Magento\Customer\Model\CustomerFactory')
            ->create();
        $customerModel->load($customerData[Customer::ID]);
        $rpToken = 'lsdj579slkj5987slkj595lkj';
        $customerModel->setRpToken('lsdj579slkj5987slkj595lkj');
        $customerModel->setRpTokenCreatedAt(date('Y-m-d'));
        $customerModel->save();
        $path = self::RESOURCE_PATH . '/' . $customerData[Customer::ID] . '/validateResetPasswordLinkToken/' . $rpToken;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => $path,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'ValidateResetPasswordLinkToken'
            ]
        ];
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $this->_webApiCall(
                $serviceInfo,
                ['customerId' => $customerData['id'], 'resetPasswordLinkToken' => $rpToken]
            );
        } else {
            $this->_webApiCall($serviceInfo);
        }
    }

    public function testValidateResetPasswordLinkTokenInvalidToken()
    {
        $customerData = $this->_createSampleCustomer();
        $path = self::RESOURCE_PATH . '/' . $customerData[Customer::ID] . '/validateResetPasswordLinkToken/invalid';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => $path,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'ValidateResetPasswordLinkToken'
            ]
        ];
        try {
            if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
                $this->_webApiCall(
                    $serviceInfo,
                    ['customerId' => $customerData['id'], 'resetPasswordLinkToken' => 'invalid']
                );
            } else {
                $this->_webApiCall($serviceInfo);
            }
        } catch (\Exception $e) {
            $errorObj = $this->_processRestExceptionResult($e);
            $this->assertEquals("Reset password token mismatch.", $errorObj['message']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_BAD_REQUEST, $e->getCode());
        }
    }

    public function testInitiatePasswordReset()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/initiatePasswordReset',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'InitiatePasswordReset'
            ]
        ];
        $requestData = [
            'email' => $customerData[Customer::EMAIL],
            'template' => CustomerAccountServiceInterface::EMAIL_RESET,
            'websiteId' => $customerData[Customer::WEBSITE_ID]
        ];
        // This api doesn't return any response.
        // No exception or response means the request was processed successfully.
        // The webapi framework does not return the header information as yet. A check for HTTP 200 would be ideal here
        $this->_webApiCall($serviceInfo, $requestData);
    }

    public function testSendPasswordResetLinkBadEmailOrWebsite()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/initiatePasswordReset',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'InitiatePasswordReset'
            ]
        ];
        $requestData = [
            'email' => 'dummy@example.com',
            'template' => CustomerAccountServiceInterface::EMAIL_RESET,
            'websiteId' => 0
        ];
        try {
            $this->_webApiCall($serviceInfo, $requestData);
        } catch (\Exception $e) {
            $errorObj = $this->_processRestExceptionResult($e);
            $this->assertEquals(
                NoSuchEntityException::MESSAGE_DOUBLE_FIELDS,
                $errorObj['message']
            );
            $this->assertEquals([
                    'fieldName' => 'email',
                    'fieldValue' => 'dummy@example.com',
                    'field2Name' => 'websiteId',
                    'field2Value' => 0,
                ], $errorObj['parameters']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_NOT_FOUND, $e->getCode());
        }

    }

    public function testGetConfirmationStatus()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::ID] . '/confirmationStatus',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetConfirmationStatus'
            ]
        ];
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $confirmationResponse = $this->_webApiCall($serviceInfo, ['customerId' => $customerData['id']]);
        } else {
            $confirmationResponse = $this->_webApiCall($serviceInfo);
        }
        $this->assertEquals(CustomerAccountServiceInterface::ACCOUNT_CONFIRMATION_NOT_REQUIRED, $confirmationResponse);
    }

    public function testResendConfirmation()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/resendConfirmation',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'ResendConfirmation'
            ]
        ];
        $requestData = [
            'email' => $customerData[Customer::EMAIL],
            'websiteId' => $customerData[Customer::WEBSITE_ID]
        ];
        // This api doesn't return any response.
        // No exception or response means the request was processed successfully.
        // The webapi framework does not return the header information as yet. A check for HTTP 200 would be ideal here
        $this->_webApiCall($serviceInfo, $requestData);
    }

    public function testResendConfirmationBadEmailOrWebsite()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/resendConfirmation',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'ResendConfirmation'
            ]
        ];
        $requestData = [
            'email' => 'dummy@example.com',
            'websiteId' => 0
        ];
        try {
            $this->_webApiCall($serviceInfo, $requestData);
        } catch (\Exception $e) {
            $errorObj = $this->_processRestExceptionResult($e);
            $this->assertEquals(
                'No such entity with %fieldName = %fieldValue, %field2Name = %field2Value',
                $errorObj['message']
            );
            $this->assertEquals([
                    'fieldName' => 'email',
                    'fieldValue' => 'dummy@example.com',
                    'field2Name' => 'websiteId',
                    'field2Value' => 0,
                ], $errorObj['parameters']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    public function testValidateCustomerData()
    {
        $customerData = $this->_createSampleCustomerDataObject();
        $customerData = $this->customerBuilder->populate($customerData)
            ->setFirstname(null)->setLastname(null)->create();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/validateCustomerData',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'ValidateCustomerData'
            ]
        ];
        $requestData = ['customer' => $customerData->__toArray(), 'attributes' => []];
        $validationResponse = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertFalse($validationResponse['valid']);
        $this->assertEquals('The first name cannot be empty.', $validationResponse['messages'][0]);
        $this->assertEquals('The last name cannot be empty.', $validationResponse['messages'][1]);
    }

    public function testCanModify()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::ID] . '/canModify',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'CanModify'
            ]
        ];
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $response = $this->_webApiCall($serviceInfo, ['customerId' => $customerData['id']]);
        } else {
            $response = $this->_webApiCall($serviceInfo);
        }
        $this->assertTrue($response);
    }

    public function testCanDelete()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::ID] . '/canDelete',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'CanDelete'
            ]
        ];
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $response = $this->_webApiCall($serviceInfo, ['customerId' => $customerData['id']]);
        } else {
            $response = $this->_webApiCall($serviceInfo);
        }
        $this->assertTrue($response);
    }

    public function testDeleteCustomer()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::ID],
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'DeleteCustomer'
            ]
        ];
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $response = $this->_webApiCall($serviceInfo, ['customerId' => $customerData['id']]);
        } else {
            $response = $this->_webApiCall($serviceInfo);
        }

        $this->assertTrue($response);

        //Verify if the customer is deleted
        $this->setExpectedException(
            'Magento\Framework\Exception\NoSuchEntityException',
            sprintf("No such entity with customerId = %s", $customerData[Customer::ID])
        );
        $this->_getCustomerDetails($customerData[Customer::ID]);
    }

    public function testDeleteCustomerInvalidCustomerId()
    {
        $invalidId = -1;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $invalidId,
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'DeleteCustomer'
            ]
        ];

        $expectedMessage = 'No such entity with %fieldName = %fieldValue';

        try {
            if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
                $this->_webApiCall($serviceInfo, ['customerId' => $invalidId]);
            } else {
                $this->_webApiCall($serviceInfo);
            }
            $this->fail("Expected exception");
        } catch (\SoapFault $e) {
            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "SoapFault does not contain expected message."
            );
        } catch (\Exception $e) {
            $errorObj = $this->_processRestExceptionResult($e);
            $this->assertEquals($expectedMessage, $errorObj['message']);
            $this->assertEquals(['fieldName' => 'customerId', 'fieldValue' => $invalidId], $errorObj['parameters']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    public function testEmailAvailable()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/isEmailAvailable',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'IsEmailAvailable'
            ]
        ];
        $requestData = [
            'customerEmail' => $customerData[Customer::EMAIL],
            'websiteId' => $customerData[Customer::WEBSITE_ID]
        ];
        $this->assertFalse($this->_webApiCall($serviceInfo, $requestData));
    }

    public function testEmailAvailableInvalidEmail()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/isEmailAvailable',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'IsEmailAvailable'
            ]
        ];
        $requestData = [
            'customerEmail' => 'invalid',
            'websiteId' => 0
        ];
        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));
    }

    public function testUpdateCustomer()
    {
        $customerData = $this->_createSampleCustomer();
        $customerDetails = $this->_getCustomerDetails($customerData[Customer::ID]);
        $lastName = $customerDetails->getCustomer()->getLastname();

        $updatedCustomer = $this->customerBuilder->populate($customerDetails->getCustomer())->setLastname(
            $lastName . "Updated"
        )->create();

        $updatedCustomerDetails = $this->customerDetailsBuilder->populate($customerDetails)->setCustomer(
            $updatedCustomer
        )->setAddresses($customerDetails->getAddresses())->create();


        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'UpdateCustomer'
            ]
        ];
        $customerDetailsAsArray = $updatedCustomerDetails->__toArray();
        $requestData = ['customerDetails' => $customerDetailsAsArray];
        $response = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue($response);

        //Verify if the customer is updated
        $customerDetails = $this->_getCustomerDetails($customerData[Customer::ID]);
        $this->assertEquals($lastName . "Updated", $customerDetails->getCustomer()->getLastname());
    }

    public function testUpdateCustomerException()
    {
        $customerData = $this->_createSampleCustomer();
        $customerDetails = $this->_getCustomerDetails($customerData[Customer::ID]);
        $lastName = $customerDetails->getCustomer()->getLastname();

        //Set non-existent id = -1
        $updatedCustomer = $this->customerBuilder->populate($customerDetails->getCustomer())->setLastname(
            $lastName . "Updated"
        )->setId(-1)->create();

        $updatedCustomerDetails = $this->customerDetailsBuilder->populate($customerDetails)->setCustomer(
            $updatedCustomer
        )->setAddresses($customerDetails->getAddresses())->create();


        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'UpdateCustomer'
            ]
        ];
        $customerDetailsAsArray = $updatedCustomerDetails->__toArray();
        $requestData = ['customerDetails' => $customerDetailsAsArray];

        $expectedMessage = 'No such entity with %fieldName = %fieldValue';

        try {
            $this->_webApiCall($serviceInfo, $requestData);
            $this->fail("Expected exception.");
        } catch (\SoapFault $e) {
            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "SoapFault does not contain expected message."
            );
        } catch (\Exception $e) {
            $errorObj = $this->_processRestExceptionResult($e);
            $this->assertEquals($expectedMessage, $errorObj['message']);
            $this->assertEquals(['fieldName' => 'customerId', 'fieldValue' => -1], $errorObj['parameters']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    /**
     * Test with a single filter
     */
    public function testSearchCustomers()
    {
        $customerData = $this->_createSampleCustomer();
        $filter = (new FilterBuilder())
            ->setField(Customer::EMAIL)
            ->setValue($customerData[Customer::EMAIL])
            ->create();
        $this->searchCriteriaBuilder->addFilter([$filter]);
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/search',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'SearchCustomers'
            ]
        ];
        $searchData = $this->searchCriteriaBuilder->create()->__toArray();
        $requestData = ['searchCriteria' => $searchData];
        $searchResults = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(1, $searchResults['total_count']);
        $this->assertEquals($customerData[Customer::ID], $searchResults['items'][0]['customer'][Customer::ID]);
    }

    /**
     * Test using multiple filters
     */
    public function testSearchCustomersMultipleFiltersWithSort()
    {
        $customerData1 = $this->_createSampleCustomer();
        $customerData2 = $this->_createSampleCustomer();
        $filter1 = (new FilterBuilder())
            ->setField(Customer::EMAIL)
            ->setValue($customerData1[Customer::EMAIL])
            ->create();
        $filter2 = (new FilterBuilder())
            ->setField(Customer::EMAIL)
            ->setValue($customerData2[Customer::EMAIL])
            ->create();
        $filter3 = (new FilterBuilder())
            ->setField(Customer::LASTNAME)
            ->setValue($customerData1[Customer::LASTNAME])
            ->create();
        $this->searchCriteriaBuilder->addFilter([$filter1, $filter2]);
        $this->searchCriteriaBuilder->addFilter([$filter3]);
        $this->searchCriteriaBuilder->setSortOrders([Customer::EMAIL => SearchCriteria::SORT_ASC]);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/search',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'SearchCustomers'
            ]
        ];
        $searchData = $searchCriteria->__toArray();
        $requestData = ['searchCriteria' => $searchData];
        $searchResults = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(2, $searchResults['total_count']);
        $this->assertEquals($customerData1[Customer::ID], $searchResults['items'][0]['customer'][Customer::ID]);
        $this->assertEquals($customerData2[Customer::ID], $searchResults['items'][1]['customer'][Customer::ID]);
    }

    /**
     * Test and verify multiple filters using And-ed non-existent filter value
     */
    public function testSearchCustomersNonExistentMultipleFilters()
    {
        $customerData1 = $this->_createSampleCustomer();
        $customerData2 = $this->_createSampleCustomer();
        $filter1 = (new FilterBuilder())
            ->setField(Customer::EMAIL)
            ->setValue($customerData1[Customer::EMAIL])
            ->create();
        $filter2 = (new FilterBuilder())
            ->setField(Customer::EMAIL)
            ->setValue($customerData2[Customer::EMAIL])
            ->create();
        $filter3 = (new FilterBuilder())
            ->setField(Customer::LASTNAME)
            ->setValue('INVALID')
            ->create();
        $this->searchCriteriaBuilder->addFilter([$filter1, $filter2]);
        $this->searchCriteriaBuilder->addFilter([$filter3]);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/search',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'SearchCustomers'
            ]
        ];
        $searchData = $searchCriteria->__toArray();
        $requestData = ['searchCriteria' => $searchData];
        $searchResults = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(0, $searchResults['total_count'], 'No results expected for non-existent email.');
    }

    public function testGetCustomerByEmail()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?customerEmail='. $customerData[Customer::EMAIL],
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetCustomerByEmail'
            ]
        ];
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $customerResponseData = $this->_webApiCall(
                $serviceInfo,
                ['customerEmail' => $customerData[Customer::EMAIL]]
            );
        } else {
            $customerResponseData = $this->_webApiCall($serviceInfo);
        }
        $this->assertEquals($customerData, $customerResponseData);

    }

    public function testGetCustomerDetailsByEmail()
    {
        $customerData = $this->_createSampleCustomer();
        //Get expected details from the Service directly
        $expectedCustomerDetails = $this->customerAccountService
            ->getCustomerDetailsByEmail($customerData[Customer::EMAIL])
            ->__toArray();

        //Test GetDetails
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/details/?customerEmail=' . $customerData[Customer::EMAIL],
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetCustomerDetailsByEmail'
            ]
        ];
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $customerDetailsResponse = $this->_webApiCall(
                $serviceInfo,
                ['customerEmail' => $customerData[Customer::EMAIL]]
            );
        } else {
            $customerDetailsResponse = $this->_webApiCall($serviceInfo);
        }
        // TODO: Reset custom_attributes to empty array for now since webapi does not support it. Need to fix this.
        unset($expectedCustomerDetails['customer']['custom_attributes']);
        unset($customerDetailsResponse['customer']['customAttributes']); //For SOAP
        unset($customerDetailsResponse['customer']['custom_attributes']); //for REST

        $this->assertEquals($expectedCustomerDetails, $customerDetailsResponse);
    }

    public function testUpdateCustomerDetailsByEmail()
    {
        $customerData = $this->_createSampleCustomer();
        $customerId = $customerData[Customer::ID];
        $customerDetails = $this->_getCustomerDetails($customerId);
        $customer = $customerDetails->getCustomer();
        $customerAddress = $customerDetails->getAddresses();
        $firstName = $customer->getFirstname() . 'updated';
        $lastName = $customer->getLastname() . 'updated';
        $newEmail = 'janedoeupdated' . uniqid() . '@example.com';
        $email = $customer->getEmail();
        $city = 'San Jose';

        $customerData = array_merge(
            $customer->__toArray(),
            [
                'firstname' => $firstName,
                'lastname' => $lastName,
                'email' => $newEmail,
                'id' => null
            ]
        );

        $addressId = $customerAddress[0]->getId();
        $newAddress = array_merge($customerAddress[0]->__toArray(), ['city' => $city]);
        $this->customerBuilder->populateWithArray($customerData);
        $this->addressBuilder->populateWithArray($newAddress);
        $this->customerDetailsBuilder->setCustomer(($this->customerBuilder->create()))
            ->setAddresses(array($this->addressBuilder->create(), $customerAddress[1]));
        $updatedCustomerDetails = $this->customerDetailsBuilder->create();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/details',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'UpdateCustomerDetailsByEmail'
            ]
        ];
        $customerDetailsAsArray = $updatedCustomerDetails->__toArray();
        $requestData = ['customerEmail' => $email, 'customerDetails' => $customerDetailsAsArray];
        $response = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue($response);

        //Verify if the customer is updated
        $customerDetails = $this->_getCustomerDetails($customerId);
        $updateCustomerData = $customerDetails->getCustomer();
        $this->assertEquals($firstName, $updateCustomerData->getFirstname());
        $this->assertEquals($lastName, $updateCustomerData->getLastname());
        $this->assertEquals($newEmail, $updateCustomerData->getEmail());
        foreach ($customerDetails->getAddresses() as $newAddress) {
            if ($newAddress->getId() == $addressId) {
                $this->assertEquals($city, $newAddress->getCity());
            }
        }
    }

    public function testDeleteCustomerByEmail()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?customerEmail=' . $customerData[Customer::EMAIL] ,
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'DeleteCustomerByEmail'
            ]
        ];
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $response = $this->_webApiCall($serviceInfo, ['customerEmail' => $customerData[Customer::EMAIL]]);
        } else {
            $response = $this->_webApiCall($serviceInfo);
        }

        $this->assertTrue($response);

        //Verify if the customer is deleted
        $this->setExpectedException(
            'Magento\Framework\Exception\NoSuchEntityException',
            sprintf("No such entity with email = %s", $customerData[Customer::EMAIL])
        );
        $this->customerAccountService->getCustomerByEmail($customerData[Customer::EMAIL]);
    }

    public function testDeleteCustomerByEmailUnknownEmail()
    {
        $unknownEmail = 'unknown@email.com';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?customerEmail=' . $unknownEmail,
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'DeleteCustomerByEmail'
            ]
        ];

        $expectedMessage = NoSuchEntityException::MESSAGE_DOUBLE_FIELDS;

        try {
            if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
                $this->_webApiCall($serviceInfo, ['customerEmail' => $unknownEmail]);
            } else {
                $this->_webApiCall($serviceInfo);
            }
            $this->fail("Expected exception");
        } catch (\SoapFault $e) {
            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "SoapFault does not contain expected message."
            );
        } catch (\Exception $e) {
            $errorObj = $this->_processRestExceptionResult($e);
            $this->assertEquals($expectedMessage, $errorObj['message']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    /**
     * Test using multiple filters
     */
    public function testSearchCustomersMultipleFilterGroups()
    {
        $customerData1 = $this->_createSampleCustomer();

        $filter1 = (new FilterBuilder())
            ->setField(Customer::EMAIL)
            ->setValue($customerData1[Customer::EMAIL])
            ->create();
        $filter2 = (new FilterBuilder())
            ->setField(Customer::MIDDLENAME)
            ->setValue($customerData1[Customer::MIDDLENAME])
            ->create();
        $filter3 = (new FilterBuilder())
            ->setField(Customer::MIDDLENAME)
            ->setValue('invalid')
            ->create();
        $filter4 = (new FilterBuilder())
            ->setField(Customer::LASTNAME)
            ->setValue($customerData1[Customer::LASTNAME])
            ->create();

        $this->searchCriteriaBuilder->addFilter([$filter1]);
        $this->searchCriteriaBuilder->addFilter([$filter2, $filter3]);
        $this->searchCriteriaBuilder->addFilter([$filter4]);
        $searchCriteria = $this->searchCriteriaBuilder->setCurrentPage(1)->setPageSize(10)->create();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/search',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'SearchCustomers'
            ]
        ];
        $searchData = $searchCriteria->__toArray();
        $requestData = ['searchCriteria' => $searchData];
        $searchResults = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(1, $searchResults['total_count']);
        $this->assertEquals($customerData1[Customer::ID], $searchResults['items'][0]['customer'][Customer::ID]);

        // Add an invalid And-ed data with multiple groups to yield no result
        $filter4 = (new FilterBuilder())
            ->setField(Customer::LASTNAME)
            ->setValue('invalid')
            ->create();

        $this->searchCriteriaBuilder->addFilter([$filter1]);
        $this->searchCriteriaBuilder->addFilter([$filter2, $filter3]);
        $this->searchCriteriaBuilder->addFilter([$filter4]);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchData = $searchCriteria->__toArray();
        $requestData = ['searchCriteria' => $searchData];
        $searchResults = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(0, $searchResults['total_count']);
    }

    /**
     * @magentoApiDataFixture Magento/Customer/_files/attribute_user_defined_address.php
     * @magentoApiDataFixture Magento/Customer/_files/attribute_user_defined_customer.php
     */
    public function testCustomAttributes()
    {
        //Sample customer data comes with the  disable_auto_group_change custom attribute
        $customerDetails = $this->_createSampleCustomerDetailsData();
        //address attribute code from fixture
        $fixtureAddressAttributeCode = 'address_user_attribute';
        //customer attribute code from fixture
        $fixtureCustomerAttributeCode = 'user_attribute';
        //Custom Attribute Values
        $address1CustomAttributeValue = 'value1';
        $address2CustomAttributeValue = 'value2';
        $customerCustomAttributeValue = 'value3';

        //Verify if the custom attributes are saved from  the fixture
        $this->assertTrue(in_array($fixtureCustomerAttributeCode, $this->customerBuilder->getCustomAttributesCodes()));
        $this->assertTrue(in_array($fixtureAddressAttributeCode, $this->addressBuilder->getCustomAttributesCodes()));

        $address1 = $this->addressBuilder
            ->populate($customerDetails->getAddresses()[0])
            ->setCustomAttribute($fixtureAddressAttributeCode, $address1CustomAttributeValue)
            ->create();
        $address2 = $this->addressBuilder
            ->populate($customerDetails->getAddresses()[1])
            ->setCustomAttribute($fixtureAddressAttributeCode, $address2CustomAttributeValue)
            ->create();

        $customer = $this->customerBuilder
            ->populate($customerDetails->getCustomer())
            ->setCustomAttribute($fixtureCustomerAttributeCode, $customerCustomAttributeValue)
            ->create();

        $customerDetails = $this->customerDetailsBuilder
            ->setAddresses([$address1, $address2])
            ->setCustomer($customer)
            ->create();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'CreateCustomer'
            ]
        ];

        $customerDetailsAsArray = $customerDetails->__toArray();
        $requestData = ['customerDetails' => $customerDetailsAsArray, 'password' => 'test@123'];
        $customerData = $this->_webApiCall($serviceInfo, $requestData);
        //TODO: Fix assertions to verify custom attributes
        $this->assertNotNull($customerData);
    }

    /**
     * @return CustomerDetails
     */
    private function _createSampleCustomerDetailsData()
    {
        $this->addressBuilder
            ->setCountryId('US')
            ->setDefaultBilling(true)
            ->setDefaultShipping(true)
            ->setPostcode('75477')
            ->setRegion((new RegionBuilder())->setRegionCode('AL')->setRegion('Alabama')->setRegionId(1)->create())
            ->setStreet(['Green str, 67'])
            ->setTelephone('3468676')
            ->setCity('CityM')
            ->setFirstname('John')
            ->setLastname('Smith');
        $address1 = $this->addressBuilder->create();

        $this->addressBuilder
            ->setCountryId('US')
            ->setDefaultBilling(false)
            ->setDefaultShipping(false)
            ->setPostcode('47676')
            ->setRegion((new RegionBuilder())->setRegionCode('AL')->setRegion('Alabama')->setRegionId(1)->create())
            ->setStreet(['Black str, 48', 'Building D'])
            ->setCity('CityX')
            ->setTelephone('3234676')
            ->setFirstname('John')
            ->setLastname('Smith');

        $address2 = $this->addressBuilder->create();

        $customerData = $this->_createSampleCustomerDataObject();
        $customerDetails = $this->customerDetailsBuilder->setAddresses([$address1, $address2])
            ->setCustomer($customerData)
            ->create();
        return $customerDetails;
    }

    /**
     * Create customer using setters.
     *
     * @return Customer
     */
    private function _createSampleCustomerDataObject()
    {
        $customerData = [
            Customer::FIRSTNAME => self::FIRSTNAME,
            Customer::LASTNAME => self::LASTNAME,
            Customer::EMAIL => 'janedoe' . uniqid() . '@example.com',
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
            Customer::WEBSITE_ID => self::WEBSITE_ID,
            Customer::CUSTOM_ATTRIBUTES_KEY => [
                [
                    'attribute_code' => 'disable_auto_group_change',
                    'value' => '0'
                ]
            ]
        ];
        return $this->customerBuilder->populateWithArray($customerData)->create();
    }

    /**
     * Create sample customer data using REST api
     *
     * @return array
     */
    protected function _createSampleCustomer()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'CreateCustomer'
            ]
        ];
        $customerDetailsAsArray = $this->_createSampleCustomerDetailsData()->__toArray();
        $requestData = ['customerDetails' => $customerDetailsAsArray, 'password' => 'test@123'];
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

    /**
     * Return the customer details.
     *
     * @param int $customerId
     * @return \Magento\Customer\Service\V1\Data\CustomerDetails
     */
    protected function _getCustomerDetails($customerId)
    {
        $details =  $this->customerAccountService->getCustomerDetails($customerId);
        $this->customerRegistry->remove($customerId);
        return $details;
    }
}
