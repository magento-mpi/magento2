<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Api;

use Magento\Customer\Service\V1\Data\Customer;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteria;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Helper\Customer as CustomerHelper;
use Magento\Webapi\Exception as HTTPExceptionCodes;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class CustomerAccountServiceTest
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomerRepositoryTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'customerCustomerRepositoryV1';
    const RESOURCE_PATH = '/V1/customers';

    /**
     * Sample values for testing
     */
    const ATTRIBUTE_CODE = 'attribute_code';
    const ATTRIBUTE_VALUE = 'attribute_value';

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Customer\Api\Data\AddressDataBuilder
     */
    private $addressBuilder;

    /**
     * @var \Magento\Customer\Api\Data\CustomerDataBuilder
     */
    private $customerBuilder;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var CustomerHelper
     */
    private $customerHelper;

    /**
     * @var array
     */
    private $currentCustomerId;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $this->customerRegistry = Bootstrap::getObjectManager()->get(
            'Magento\Customer\Model\CustomerRegistry'
        );

        $this->customerRepository = Bootstrap::getObjectManager()->get(
            'Magento\Customer\Api\CustomerRepositoryInterface',
            ['customerRegistry' => $this->customerRegistry]
        );
        $this->addressBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Api\Data\AddressDataBuilder'
        );
        $this->customerBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Api\Data\CustomerDataBuilder'
        );
        $this->searchCriteriaBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Api\SearchCriteriaBuilder'
        );
        $this->sortOrderBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Api\SortOrderBuilder'
        );
        $this->filterGroupBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Api\Search\FilterGroupBuilder'
        );
        $this->customerHelper = new CustomerHelper();

        $this->dataObjectProcessor = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Reflection\DataObjectProcessor'
        );
    }

    public function tearDown()
    {
        if (!empty($this->currentCustomerId)) {
            foreach ($this->currentCustomerId as $customerId) {
                $serviceInfo = [
                    'rest' => [
                        'resourcePath' => self::RESOURCE_PATH . '/' . $customerId,
                        'httpMethod' => RestConfig::HTTP_METHOD_DELETE
                    ],
                    'soap' => [
                        'service' => self::SERVICE_NAME,
                        'serviceVersion' => self::SERVICE_VERSION,
                        'operation' => self::SERVICE_NAME . 'DeleteCustomer'
                    ]
                ];

                $response = $this->_webApiCall($serviceInfo, ['customerId' => $customerId]);

                $this->assertTrue($response);
            }
        }
        unset($this->customerRepository);
    }

    public function testDeleteCustomer()
    {
        $customerData = $this->_createCustomer();
        $this->currentCustomerId = [];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $customerData[Customer::ID],
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'DeleteById'
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
        $this->_getCustomerData($customerData[Customer::ID]);
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
                'operation' => self::SERVICE_NAME . 'DeleteById'
            ]
        ];

        $expectedMessage = 'No such entity with %fieldName = %fieldValue';

        try {

            $this->_webApiCall($serviceInfo, ['customerId' => $invalidId]);

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

    public function testDeleteCustomerByEmail()
    {
        $this->markTestSkipped();
        $customerData = $this->_createCustomer();
        $this->currentCustomerId = [];
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
        $this->customerRepository->getCustomerByEmail($customerData[Customer::EMAIL]);
    }

    public function testDeleteCustomerByEmailUnknownEmail()
    {
        $this->markTestSkipped();
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
            $this->_webApiCall($serviceInfo, ['customerEmail' => $unknownEmail]);
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

    public function testUpdateCustomer()
    {
        $customerData = $this->_createCustomer();
        $existingCustomerDataObject = $this->_getCustomerData($customerData[Customer::ID]);
        $lastName = $existingCustomerDataObject->getLastname();
        $customerData[Customer::LASTNAME] = $lastName . 'Updated';
        $newCustomerDataObject = $this->customerBuilder->populateWithArray($customerData)->create();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/{$customerData[Customer::ID]}",
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Save'
            ]
        ];
        $newCustomerDataObject = $this->dataObjectProcessor->buildOutputDataArray(
            $newCustomerDataObject,
            'Magento\Customer\Api\Data\CustomerInterface'
        );
        $requestData = ['customer' => $newCustomerDataObject];
        $response = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue(!is_null($response));

        //Verify if the customer is updated
        $existingCustomerDataObject = $this->_getCustomerData($customerData[Customer::ID]);
        $this->assertEquals($lastName . "Updated", $existingCustomerDataObject->getLastname());
    }

    /**
     * Verify expected behavior when the website id is not set
     */
    public function testUpdateCustomerNoWebsiteId()
    {
        $customerData = $this->customerHelper->createSampleCustomer();
        $existingCustomerDataObject = $this->_getCustomerData($customerData[Customer::ID]);
        $lastName = $existingCustomerDataObject->getLastname();
        $customerData[Customer::LASTNAME] = $lastName . 'Updated';
        $newCustomerDataObject = $this->customerBuilder->populateWithArray($customerData)->create();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/{$customerData[Customer::ID]}",
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Save'
            ]
        ];
        $newCustomerDataObject = $this->dataObjectProcessor->buildOutputDataArray(
            $newCustomerDataObject,
            'Magento\Customer\Api\Data\CustomerInterface'
        );
        unset($newCustomerDataObject['website_id']);
        $requestData = ['customer' => $newCustomerDataObject];

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
        $customerData = $this->_createCustomer();
        $existingCustomerDataObject = $this->_getCustomerData($customerData[Customer::ID]);
        $lastName = $existingCustomerDataObject->getLastname();

        //Set non-existent id = -1
        $customerData[Customer::LASTNAME] = $lastName . 'Updated';
        $customerData[Customer::ID] = -1;

        $newCustomerDataObject = $this->customerBuilder->populateWithArray($customerData)->create();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/-1",
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Save'
            ]
        ];
        $newCustomerDataObject = $this->dataObjectProcessor->buildOutputDataArray(
            $newCustomerDataObject,
            'Magento\Customer\Api\Data\CustomerInterface'
        );
        $requestData = ['customer' => $newCustomerDataObject];

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

    public function testUpdateCustomerByEmail()
    {
        $this->markTestSkipped();
        $customerData = $this->_createCustomer();
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
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'UpdateCustomerByEmail'
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

    /**
     * Test with a single filter
     */
    public function testSearchCustomers()
    {
        $builder = Bootstrap::getObjectManager()->create('Magento\Framework\Api\FilterBuilder');
        $customerData = $this->_createCustomer();
        $filter = $builder
            ->setField(Customer::EMAIL)
            ->setValue($customerData[Customer::EMAIL])
            ->create();
        $this->searchCriteriaBuilder->addFilter([$filter]);
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/search',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'getList'
            ]
        ];
        $searchData = $this->dataObjectProcessor->buildOutputDataArray(
            $this->searchCriteriaBuilder->create(),
            'Magento\Framework\Api\SearchCriteriaInterface'
        );
        $requestData = ['searchCriteria' => $searchData];
        $searchResults = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(1, $searchResults['total_count']);
        $this->assertEquals($customerData[Customer::ID], $searchResults['items'][0][Customer::ID]);
    }

    /**
     * Test with a single filter using GET
     */
    public function testSearchCustomersUsingGET()
    {
        $this->_markTestAsRestOnly('SOAP test is covered in testSearchCustomers');
        $builder = Bootstrap::getObjectManager()->create('Magento\Framework\Api\FilterBuilder');
        $customerData = $this->_createCustomer();
        $filter = $builder
            ->setField(Customer::EMAIL)
            ->setValue($customerData[Customer::EMAIL])
            ->create();
        $this->searchCriteriaBuilder->addFilter([$filter]);

        $searchData = $this->searchCriteriaBuilder->create()->__toArray();
        $requestData = ['searchCriteria' => $searchData];
        $searchQueryString = http_build_query($requestData);
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/search?' . $searchQueryString,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ]
        ];
        $searchResults = $this->_webApiCall($serviceInfo);
        $this->assertEquals(1, $searchResults['total_count']);
        $this->assertEquals($customerData[Customer::ID], $searchResults['items'][0][Customer::ID]);
    }

    /**
     * Test using multiple filters
     */
    public function testSearchCustomersMultipleFiltersWithSort()
    {
        $builder = Bootstrap::getObjectManager()->create('Magento\Framework\Api\FilterBuilder');
        $customerData1 = $this->_createCustomer();
        $customerData2 = $this->_createCustomer();
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

        /**@var \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder */
        $sortOrderBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Api\SortOrderBuilder'
        );
        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        $sortOrder = $sortOrderBuilder->setField(Customer::EMAIL)->setDirection(SearchCriteria::SORT_ASC)->create();
        $this->searchCriteriaBuilder->setSortOrders([$sortOrder]);

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/search',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'getList'
            ]
        ];
        $searchData = $searchCriteria->__toArray();
        $requestData = ['searchCriteria' => $searchData];
        $searchResults = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(2, $searchResults['total_count']);
        $this->assertEquals($customerData1[Customer::ID], $searchResults['items'][0][Customer::ID]);
        $this->assertEquals($customerData2[Customer::ID], $searchResults['items'][1][Customer::ID]);
    }

    /**
     * Test using multiple filters using GET
     */
    public function testSearchCustomersMultipleFiltersWithSortUsingGET()
    {
        $this->_markTestAsRestOnly('SOAP test is covered in testSearchCustomers');
        $builder = Bootstrap::getObjectManager()->create('Magento\Framework\Api\FilterBuilder');
        $customerData1 = $this->_createCustomer();
        $customerData2 = $this->_createCustomer();
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
        $this->searchCriteriaBuilder->setSortOrders([Customer::EMAIL => SearchCriteria::SORT_ASC]);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchData = $searchCriteria->__toArray();
        $requestData = ['searchCriteria' => $searchData];
        $searchQueryString = http_build_query($requestData);
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/search?' . $searchQueryString,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ]
        ];
        $searchResults = $this->_webApiCall($serviceInfo);
        $this->assertEquals(2, $searchResults['total_count']);
        $this->assertEquals($customerData1[Customer::ID], $searchResults['items'][0][Customer::ID]);
        $this->assertEquals($customerData2[Customer::ID], $searchResults['items'][1][Customer::ID]);
    }

    /**
     * Test and verify multiple filters using And-ed non-existent filter value
     */
    public function testSearchCustomersNonExistentMultipleFilters()
    {
        $builder = Bootstrap::getObjectManager()->create('Magento\Framework\Api\FilterBuilder');
        $customerData1 = $this->_createCustomer();
        $customerData2 = $this->_createCustomer();
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
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'getList'
            ]
        ];
        $searchData = $searchCriteria->__toArray();
        $requestData = ['searchCriteria' => $searchData];
        $searchResults = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(0, $searchResults['total_count'], 'No results expected for non-existent email.');
    }

    /**
     * Test and verify multiple filters using And-ed non-existent filter value using GET
     */
    public function testSearchCustomersNonExistentMultipleFiltersGET()
    {
        $this->_markTestAsRestOnly('SOAP test is covered in testSearchCustomers');
        $builder = Bootstrap::getObjectManager()->create('Magento\Framework\Api\FilterBuilder');
        $customerData1 = $this->_createCustomer();
        $customerData2 = $this->_createCustomer();
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
        $searchData = $searchCriteria->__toArray();
        $requestData = ['searchCriteria' => $searchData];
        $searchQueryString = http_build_query($requestData);
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/search?' . $searchQueryString,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ]
        ];
        $searchResults = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(0, $searchResults['total_count'], 'No results expected for non-existent email.');
    }

    /**
     * Test using multiple filters
     */
    public function testSearchCustomersMultipleFilterGroups()
    {
        $customerData1 = $this->_createCustomer();

        /** @var \Magento\Framework\Api\FilterBuilder $builder */
        $builder = Bootstrap::getObjectManager()->create('Magento\Framework\Api\FilterBuilder');
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
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'getList'
            ]
        ];
        $searchData = $searchCriteria->__toArray();
        $requestData = ['searchCriteria' => $searchData];
        $searchResults = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(1, $searchResults['total_count']);
        $this->assertEquals($customerData1[Customer::ID], $searchResults['items'][0][Customer::ID]);

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
     * Retrieve customer data by Id
     *
     * @param int $customerId
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    protected function _getCustomerData($customerId)
    {
        $customerData =  $this->customerRepository->getById($customerId);
        $this->customerRegistry->remove($customerId);
        return $customerData;
    }

    /**
     * @return array|bool|float|int|string
     */
    protected function _createCustomer()
    {
        $customerData = $this->customerHelper->createSampleCustomer();
        $this->currentCustomerId[] = $customerData['id'];
        return $customerData;
    }
}
