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
use Magento\Framework\Service\V1\Data\SearchCriteria;
use Magento\Webapi\Exception as HTTPExceptionCodes;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Service\V1\Data\Customer;
use Magento\Customer\Service\V1\Data\CustomerBuilder;
use Magento\Customer\Service\V1\Data\CustomerDetailsBuilder;
use Magento\Framework\Service\V1\Data\Search\FilterGroupBuilder;
use Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Helper\Customer as CustomerHelper;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\Framework\Exception\InputException;

/**
 * Class CustomerAccountServiceTest
 * 
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomerAccountServiceTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'customerCustomerAccountServiceV1';
    const RESOURCE_PATH = '/V1/customers';

    /** Sample values for testing */
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

    /** @var CustomerHelper */
    private $customerHelper;

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
            ['customerRegistry' => $this->customerRegistry]
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
        $this->customerHelper = new CustomerHelper();
    }

    public function tearDown()
    {
        unset($this->customerAccountService);
        $model = Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Attribute');
        $model->load('address_user_attribute', 'attribute_code')
            ->delete();
        $model = Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Attribute');
        $model->load('user_attribute', 'attribute_code')
            ->delete();
    }

    public function testCreateCustomer()
    {
        $customerData = $this->customerHelper->createSampleCustomer();
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

        $customerDetailsAsArray = $this->customerHelper->createSampleCustomerDetailsData()->__toArray();
        unset($customerDetailsAsArray['customer']['firstname']);
        unset($customerDetailsAsArray['customer']['email']);
        $requestData = ['customerDetails' => $customerDetailsAsArray, 'password' => CustomerHelper::PASSWORD];
        try {
            $this->_webApiCall($serviceInfo, $requestData);
            $this->fail('Expected exception did not occur.');
        } catch (\Exception $e) {
            if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
                $this->assertInstanceOf('SoapFault', $e);
                $exceptionData = $e->getMessage();
                $expectedExceptionData = "SOAP-ERROR: Encoding: object has no 'email' property";
            } else {
                $this->assertEquals(HTTPExceptionCodes::HTTP_BAD_REQUEST, $e->getCode());
                $exceptionData = $this->processRestExceptionResult($e);
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
        $customerData = $this->customerHelper->createSampleCustomer();

        //Get expected details from the Service directly
        $expectedCustomerDetails = $this->_getCustomerDetails($customerData['id'])->__toArray();

        //Test GetDetails
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData['id'] . '/details',
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
        $customerData = $this->customerHelper->createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::ID],
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
        $customerData = $this->customerHelper->createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::ID] . '/activate',
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
        $customerData = $this->customerHelper->createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/login',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Authenticate'
            ]
        ];
        $requestData = ['username' => $customerData[Customer::EMAIL], 'password' => CustomerHelper::PASSWORD];
        $customerResponseData = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($customerData[Customer::ID], $customerResponseData[Customer::ID]);
    }

    public function testValidateResetPasswordLinkToken()
    {
        $customerData = $this->customerHelper->createSampleCustomer();
        /** @var \Magento\Customer\Model\Customer $customerModel */
        $customerModel = Bootstrap::getObjectManager()->create('Magento\Customer\Model\CustomerFactory')
            ->create();
        $customerModel->load($customerData[Customer::ID]);
        $rpToken = 'lsdj579slkj5987slkj595lkj';
        $customerModel->setRpToken('lsdj579slkj5987slkj595lkj');
        $customerModel->setRpTokenCreatedAt(date('Y-m-d'));
        $customerModel->save();
        $path = self::RESOURCE_PATH . '/' . $customerData[Customer::ID] . '/password/resetLinkToken/' . $rpToken;
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
        $customerData = $this->customerHelper->createSampleCustomer();
        $invalidToken = 'fjjkafjie';
        $path = self::RESOURCE_PATH . '/' . $customerData[Customer::ID] . '/password/resetLinkToken/' . $invalidToken;
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
            $errorObj = $this->processRestExceptionResult($e);
            $this->assertEquals("Reset password token mismatch.", $errorObj['message']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_BAD_REQUEST, $e->getCode());
        }
    }

    public function testInitiatePasswordReset()
    {
        $customerData = $this->customerHelper->createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/password',
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
                'resourcePath' => self::RESOURCE_PATH . '/password',
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
            $errorObj = $this->processRestExceptionResult($e);
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
        $customerData = $this->customerHelper->createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::ID] . '/confirm',
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
        $customerData = $this->customerHelper->createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/confirm',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
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
                'resourcePath' => self::RESOURCE_PATH . '/confirm',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
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
            $errorObj = $this->processRestExceptionResult($e);
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
        $customerData = $this->customerHelper->createSampleCustomerDataObject();
        $customerData = $this->customerBuilder->populate($customerData)
            ->setFirstname(null)->setLastname(null)->create();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/validate',
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
        $customerData = $this->customerHelper->createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::ID] . '/permissions/modify',
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
        $customerData = $this->customerHelper->createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::ID] . '/permissions/delete',
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
        $customerData = $this->customerHelper->createSampleCustomer();

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
            $errorObj = $this->processRestExceptionResult($e);
            $this->assertEquals($expectedMessage, $errorObj['message']);
            $this->assertEquals(['fieldName' => 'customerId', 'fieldValue' => $invalidId], $errorObj['parameters']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    public function testEmailAvailable()
    {
        $customerData = $this->customerHelper->createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/isEmailAvailable',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
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
                'httpMethod' => RestConfig::HTTP_METHOD_POST
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
        $customerData = $this->customerHelper->createSampleCustomer();
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

    /**
     * Verify expected behavior when the website id is not set
     */
    public function testUpdateCustomerNoWebsiteId()
    {
        $customerData = $this->customerHelper->createSampleCustomer();
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
        unset($customerDetailsAsArray['customer']['website_id']);
        $requestData = ['customerDetails' => $customerDetailsAsArray];

        $expectedMessage = '"Associate to Website" is a required value.';
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
            $errorObj =  $this->customerHelper->processRestExceptionResult($e);
            $this->assertEquals($expectedMessage, $errorObj['message'], 'Invalid message: "'.$e->getMessage().'"');
            $this->assertEquals(HTTPExceptionCodes::HTTP_BAD_REQUEST, $e->getCode());
        }
    }

    public function testUpdateCustomerException()
    {
        $customerData = $this->customerHelper->createSampleCustomer();
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
            $errorObj = $this->processRestExceptionResult($e);
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
        $builder = Bootstrap::getObjectManager()->create('Magento\Framework\Service\V1\Data\FilterBuilder');
        $customerData = $this->customerHelper->createSampleCustomer();
        $filter = $builder
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
        $builder = Bootstrap::getObjectManager()->create('Magento\Framework\Service\V1\Data\FilterBuilder');
        $customerData1 = $this->customerHelper->createSampleCustomer();
        $customerData2 = $this->customerHelper->createSampleCustomer();
        $filter1 = $builder->setField(Customer::EMAIL)
            ->setValue($customerData1[Customer::EMAIL])
            ->create();
        $filter2 = $builder->setField(Customer::EMAIL)
            ->setValue($customerData2[Customer::EMAIL])
            ->create();
        $filter3 = $builder->setField(Customer::LASTNAME)
            ->setValue($customerData1[Customer::LASTNAME])
            ->create();
        $this->searchCriteriaBuilder->addFilter([$filter1, $filter2]);
        $this->searchCriteriaBuilder->addFilter([$filter3]);

        /**@var \Magento\Framework\Service\V1\Data\SortOrderBuilder $sortOrderBuilder */
        $sortOrderBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Service\V1\Data\SortOrderBuilder'
        );
        /** @var \Magento\Framework\Service\V1\Data\SortOrder $sortOrder */
        $sortOrder = $sortOrderBuilder->setField(Customer::EMAIL)->setDirection(SearchCriteria::SORT_ASC)->create();
        $this->searchCriteriaBuilder->setSortOrders([$sortOrder]);

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
        $builder = Bootstrap::getObjectManager()->create('Magento\Framework\Service\V1\Data\FilterBuilder');
        $customerData1 = $this->customerHelper->createSampleCustomer();
        $customerData2 = $this->customerHelper->createSampleCustomer();
        $filter1 = $filter1 = $builder->setField(Customer::EMAIL)
            ->setValue($customerData1[Customer::EMAIL])
            ->create();
        $filter2 = $builder->setField(Customer::EMAIL)
            ->setValue($customerData2[Customer::EMAIL])
            ->create();
        $filter3 = $builder->setField(Customer::LASTNAME)
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
        $customerData = $this->customerHelper->createSampleCustomer();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?customerEmail=' . $customerData[Customer::EMAIL],
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
        $customerData = $this->customerHelper->createSampleCustomer();
        //Get expected details from the Service directly
        $expectedCustomerDetails = $this->customerAccountService
            ->getCustomerDetailsByEmail($customerData[Customer::EMAIL])
            ->__toArray();

        //Test GetDetails
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/details?customerEmail=' . $customerData[Customer::EMAIL],
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
        $customerData = $this->customerHelper->createSampleCustomer();
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
        $customerData = $this->customerHelper->createSampleCustomer();

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
            $errorObj = $this->processRestExceptionResult($e);
            $this->assertEquals($expectedMessage, $errorObj['message']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    /**
     * Test using multiple filters
     */
    public function testSearchCustomersMultipleFilterGroups()
    {
        $customerData1 = $this->customerHelper->createSampleCustomer();

        /** @var \Magento\Framework\Service\V1\Data\FilterBuilder $builder */
        $builder = Bootstrap::getObjectManager()->create('Magento\Framework\Service\V1\Data\FilterBuilder');
        $filter1 = $builder->setField(Customer::EMAIL)
            ->setValue($customerData1[Customer::EMAIL])
            ->create();
        $filter2 = $builder->setField(Customer::MIDDLENAME)
            ->setValue($customerData1[Customer::MIDDLENAME])
            ->create();
        $filter3 = $builder->setField(Customer::MIDDLENAME)
            ->setValue('invalid')
            ->create();
        $filter4 = $builder->setField(Customer::LASTNAME)
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
        $filter4 = $builder->setField(Customer::LASTNAME)
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
        $customerDetails = $this->customerHelper->createSampleCustomerDetailsData();
        //address attribute code from fixture
        $fixtureAddressAttributeCode = 'address_user_attribute';
        //customer attribute code from fixture
        $fixtureCustomerAttributeCode = 'user_attribute';
        //Custom Attribute Values
        $address1CustomAttributeValue = 'value1';
        $address2CustomAttributeValue = 'value2';
        $customerCustomAttributeValue = 'value3';

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
        $requestData = ['customerDetails' => $customerDetailsAsArray, 'password' => CustomerHelper::PASSWORD];
        $customerData = $this->_webApiCall($serviceInfo, $requestData);
        //TODO: Fix assertions to verify custom attributes
        $this->assertNotNull($customerData);
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
