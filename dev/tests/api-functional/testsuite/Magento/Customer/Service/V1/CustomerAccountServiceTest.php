<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1;

use Magento\Customer\Service\V1\Data\Filter;
use Magento\Customer\Service\V1\Data\FilterBuilder;
use Magento\Customer\Service\V1\Data\SearchCriteriaBuilder;
use Magento\Webapi\Exception as HTTPExceptionCodes;
use Magento\Customer\Service\V1\Data\Customer;
use Magento\Customer\Service\V1\Data\CustomerDetails;
use Magento\Customer\Service\V1\Data\CustomerDetailsBuilder;
use Magento\Customer\Service\V1\Data\CustomerBuilder;
use Magento\Customer\Service\V1\Data\AddressBuilder;
use Magento\Customer\Service\V1\Data\RegionBuilder;
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

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /** @var \Magento\Webapi\Helper\Data */
    private $helper;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
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
        $this->searchCriteriaBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\Data\SearchCriteriaBuilder'
        );
        $this->helper = Bootstrap::getObjectManager()->create('Magento\Webapi\Helper\Data');
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

    public function testGetCustomerDetails()
    {
        //Create a customer
        $customerData = $this->_createSampleCustomer();

        //Get expected details from the Service directly
        $expectedCustomerDetails = $this->customerAccountService->getCustomerDetails($customerData['id'])->__toArray();

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
            $this->assertEquals(HTTPExceptionCodes::HTTP_BAD_REQUEST, $errorObj['http_code']);
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
            $this->assertEquals("No such entity with email = dummy@example.com websiteId = 0", $errorObj['message']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_BAD_REQUEST, $errorObj['http_code']);
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
            $this->assertEquals("No such entity with email = dummy@example.com websiteId = 0", $errorObj['message']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_BAD_REQUEST, $errorObj['http_code']);
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
            'Magento\Exception\NoSuchEntityException',
            sprintf("No such entity with customerId = %s", $customerData[Customer::ID])
        );
        $this->customerAccountService->getCustomerDetails($customerData[Customer::ID]);
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

        $expectedMessage = 'No such entity with customerId = ' . $invalidId;

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
            $this->assertEquals(HTTPExceptionCodes::HTTP_BAD_REQUEST, $errorObj['http_code']);
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
        $customerDetails = $this->customerAccountService->getCustomerDetails($customerData[Customer::ID]);
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
        $customerDetails = $this->customerAccountService->getCustomerDetails($customerData[Customer::ID]);
        $this->assertEquals($lastName . "Updated", $customerDetails->getCustomer()->getLastname());
    }

    public function testUpdateCustomerWithoutId()
    {
        $customerData = $this->_createSampleCustomer();
        $customerDetails = $this->customerAccountService->getCustomerDetails($customerData[Customer::ID]);
        $lastName = $customerDetails->getCustomer()->getLastname();

        $updatedCustomer = $this->customerBuilder->populate($customerDetails->getCustomer())
            ->setLastname($lastName . "Updated")
            ->setId(null)
            ->create();

        $updatedCustomerDetails = $this->customerDetailsBuilder->populate($customerDetails)->setCustomer(
            $updatedCustomer
        )->setAddresses($customerDetails->getAddresses())->create();

        $this->assertNull($updatedCustomerDetails->getCustomer()->getId());

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
        $customerDetails = $this->customerAccountService->getCustomerDetails($customerData[Customer::ID]);
        $this->assertEquals($lastName . "Updated", $customerDetails->getCustomer()->getLastname());
    }

    public function testUpdateCustomerException()
    {
        $customerData = $this->_createSampleCustomer();
        $customerDetails = $this->customerAccountService->getCustomerDetails($customerData[Customer::ID]);
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
        $expectedMessage = 'No such entity with customerId = -1';

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
            $this->assertEquals(HTTPExceptionCodes::HTTP_BAD_REQUEST, $errorObj['http_code']);
        }
    }


    public function testSearchCustomers()
    {
        $this->markTestSkipped("The test should be enabled after fixing MAGETWO-22613.");
        $customerData = $this->_createSampleCustomer();
        $this->searchCriteriaBuilder->addFilter(
            (new FilterBuilder())->setField('email')->setValue($customerData[Customer::EMAIL])->create()
        );
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

    public function testSearchCustomersMultipleFilters()
    {
        $this->markTestSkipped("The test should be enabled after fixing MAGETWO-22613.");
        $customerData1 = $this->_createSampleCustomer();
        $customerData2 = $this->_createSampleCustomer();
        $filter1 = (new FilterBuilder())->setField('email')->setValue($customerData1[Customer::EMAIL])->create();
        $filter2 = (new FilterBuilder())->setField('email')->setValue($customerData2[Customer::EMAIL])->create();
        $filter3 = (new FilterBuilder())->setField('lastname')
            ->setValue($customerData1[Customer::LASTNAME])->create();
        $this->searchCriteriaBuilder->addOrGroup([$filter1, $filter2]);
        $this->searchCriteriaBuilder->addFilter($filter3);
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

        //Verify negative test case using And-ed non-existent lastname
        $filter1 = (new FilterBuilder())->setField('email')->setValue($customerData1[Customer::EMAIL])->create();
        $filter2 = (new FilterBuilder())->setField('email')->setValue($customerData2[Customer::EMAIL])->create();
        $filter3 = (new FilterBuilder())->setField('lastname')
            ->setValue('INVALID')->create();
        $this->searchCriteriaBuilder->addOrGroup([$filter1, $filter2]);
        $this->searchCriteriaBuilder->addFilter($filter3);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/search',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
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
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::EMAIL] . '/customerEmail',
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

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::EMAIL] . '/customerEmail',
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
            'Magento\Exception\NoSuchEntityException',
            sprintf("No such entity with email = %s", $customerData[Customer::EMAIL])
        );
        $this->customerAccountService->getCustomerByEmail($customerData[Customer::EMAIL]);
    }

    public function testDeleteCustomerByEmail()
    {
        $customerData = $this->_createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::EMAIL] . '/customerEmail',
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
            'Magento\Exception\NoSuchEntityException',
            sprintf("No such entity with email = %s", $customerData[Customer::EMAIL])
        );
        $this->customerAccountService->getCustomerByEmail($customerData[Customer::EMAIL]);
    }

    public function testDeleteCustomerByEmailInvalidCustomerId()
    {
        $invalidEmail = 'invalid@email.com';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $invalidEmail . '/customerEmail',
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'DeleteCustomerByEmail'
            ]
        ];

        $expectedMessage = 'No such entity with email = ' . $invalidEmail;

        try {
            if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
                $this->_webApiCall($serviceInfo, ['customerEmail' => $invalidEmail]);
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
            $this->assertEquals(HTTPExceptionCodes::HTTP_BAD_REQUEST, $errorObj['http_code']);
        }
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
            Customer::WEBSITE_ID => self::WEBSITE_ID
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
     * array (
     *     'message' => "No such entity with email = dummy@example.com websiteId = 0"
     *     'http_code' => 400
     * )
     * </pre>
     */
    protected function _processRestExceptionResult(\Exception $e)
    {
        $error = json_decode($e->getMessage(), true)['errors'][0];
        //Remove line breaks and replace with space
        $error['message'] = trim(preg_replace('/\s+/', ' ', $error['message']));
        return $error;
    }

    /**
     * Generate a random string
     *
     * @return string
     */
    private function getRandomString()
    {
        return substr("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", mt_rand(0, 50), 1) .
        substr(md5(time()), 1);
    }
}
