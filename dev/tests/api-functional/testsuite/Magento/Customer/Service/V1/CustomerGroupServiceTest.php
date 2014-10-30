<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1;

use Magento\Customer\Model\GroupRegistry;
use Magento\Customer\Service\V1\Data\CustomerGroup;
use Magento\Customer\Service\V1\CustomerGroupService;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class CustomerGroupServiceTest
 */
class CustomerGroupServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = "customerCustomerGroupServiceV1";
    const SERVICE_VERSION = "V1";
    const RESOURCE_PATH = "/V1/customerGroups";

    /**
     * @var CustomerGroupServiceInterface
     */
    private $groupService;

    /**
     * @var GroupRegistry
     */
    private $groupRegistry;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->groupRegistry = $objectManager->get('Magento\Customer\Model\GroupRegistry');
        $this->groupService = $objectManager->get(
            'Magento\Customer\Service\V1\CustomerGroupServiceInterface',
            ['groupRegistry' => $this->groupRegistry]
        );
    }

    /**
     * Execute per test cleanup.
     */
    public function tearDown()
    {
        unset($this->groupService);
    }

    /**
     * Cleaning up the extra groups that might have been created as part of the testing.
     */
    public static function tearDownAfterClass()
    {
        /** @var CustomerGroupServiceInterface $groupService */
        $groupService = Bootstrap::getObjectManager()
            ->get('Magento\Customer\Service\V1\CustomerGroupServiceInterface');
        foreach ($groupService->getGroups() as $group) {
            if ($group->getId() > 3) {
                $groupService->deleteGroup($group->getId());
            }
        }
    }

    /**
     * Verify the retrieval of a customer group by Id.
     *
     * @param array $testGroup The group data for the group being retrieved.
     *
     * @dataProvider getGroupDataProvider
     */
    public function testGetGroup($testGroup)
    {
        $groupId = $testGroup[CustomerGroup::ID];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$groupId",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1GetGroup'
            ]
        ];
        $requestData = [CustomerGroup::ID => $groupId];
        $groupData = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertEquals($testGroup, $groupData, "The group data does not match.");
    }

    /**
     * The testGetGroup data provider.
     *
     * @return array
     */
    public function getGroupDataProvider()
    {
        return [
            'NOT LOGGED IN' => [
                [
                    CustomerGroup::ID => 0,
                    CustomerGroup::CODE => 'NOT LOGGED IN',
                    CustomerGroup::TAX_CLASS_ID => 3,
                ]
            ],
            'General' => [
                [
                    CustomerGroup::ID => 1,
                    CustomerGroup::CODE => 'General',
                    CustomerGroup::TAX_CLASS_ID => 3,
                ]
            ],
            'Wholesale' => [
                [
                    CustomerGroup::ID => 2,
                    CustomerGroup::CODE => 'Wholesale',
                    CustomerGroup::TAX_CLASS_ID => 3,
                ]
            ],
            'Retailer' => [
                [
                    CustomerGroup::ID => 3,
                    CustomerGroup::CODE => 'Retailer',
                    CustomerGroup::TAX_CLASS_ID => 3,
                ]
            ],
        ];
    }

    /**
     * Verify the retrieval of all customer groups.
     */
    public function testGetGroups()
    {
        $expectedGroups = [
            [
                CustomerGroup::ID => 0,
                CustomerGroup::CODE => 'NOT LOGGED IN',
                CustomerGroup::TAX_CLASS_ID => 3,
            ],
            [
                CustomerGroup::ID => 1,
                CustomerGroup::CODE => 'General',
                CustomerGroup::TAX_CLASS_ID => 3,
            ],
            [
                CustomerGroup::ID => 2,
                CustomerGroup::CODE => 'Wholesale',
                CustomerGroup::TAX_CLASS_ID => 3,
            ],
            [
                CustomerGroup::ID => 3,
                CustomerGroup::CODE => 'Retailer',
                CustomerGroup::TAX_CLASS_ID => 3,
            ]
        ];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1GetGroups'
            ]
        ];

        $groups = array_map(
            function ($array) {
                return $array;
            },
            $this->_webApiCall($serviceInfo)
        );

        $this->assertCount(count($expectedGroups), $groups, "The number of groups returned is wrong.");
        $this->assertEquals($expectedGroups, $groups, "The list of groups does not match.");
    }

    /**
     * Verify the retrieval of the default group for storeId equal to 1.
     *
     * @param int $storeId The store Id
     * @param array $defaultGroupData The default group data for the store with the specified Id.
     *
     * @dataProvider getDefaultGroupDataProvider
     */
    public function testGetDefaultGroup($storeId, $defaultGroupData)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/default/$storeId",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1GetDefaultGroup'
            ]
        ];
        $requestData = ['storeId' => $storeId];
        $groupData = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertEquals($defaultGroupData, $groupData, "The default group does not match.");
    }

    /**
     * The testGetDefaultGroup data provider.
     *
     * @return array
     */
    public function getDefaultGroupDataProvider()
    {
        return [
            'admin' => [
                0,
                [
                    CustomerGroup::ID => 1,
                    CustomerGroup::CODE => 'General',
                    CustomerGroup::TAX_CLASS_ID => 3,
                ]
            ],
            'base' => [
                1,
                [
                    CustomerGroup::ID => 1,
                    CustomerGroup::CODE => 'General',
                    CustomerGroup::TAX_CLASS_ID => 3,
                ]
            ]
        ];
    }

    /**
     * Verify the retrieval of a non-existent storeId will return an expected fault.
     */
    public function testGetDefaultGroupNonExistentStore()
    {
        /* Store id should not exist */
        $nonExistentStoreId = 9876;

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/default/$nonExistentStoreId",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1GetDefaultGroup'
            ]
        ];
        $requestData = ['storeId' => $nonExistentStoreId];
        $expectedMessage = 'No such entity with %fieldName = %fieldValue';

        try {
            $this->_webApiCall($serviceInfo, $requestData);
            $this->fail("Expected exception");
        } catch (\SoapFault $e) {
            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "SoapFault does not contain expected message."
            );
        } catch (\Exception $e) {
            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "Exception does not contain expected message."
            );
            $this->assertContains((string)$nonExistentStoreId, $e->getMessage());
        }
    }

    /**
     * Verify that the group with the specified Id can or cannot be deleted.
     *
     * @param int $groupId The group Id
     * @param bool $isDeleteable Whether the group can or cannot be deleted.
     *
     * @dataProvider canDeleteDataProvider
     */
    public function testCanDelete($groupId, $isDeleteable)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$groupId/permissions",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1CanDelete'
            ]
        ];

        $requestData = [CustomerGroup::ID => $groupId];

        $canDelete = $this->_webApiCall($serviceInfo, $requestData);

        $failureMessage = $isDeleteable
            ? 'The group should be deleteable.' : 'The group should not be deleteable.';
        $this->assertEquals($isDeleteable, $canDelete, $failureMessage);
    }

    /**
     * The testCanDelete data provider.
     *
     * @return array
     */
    public function canDeleteDataProvider()
    {
        return [
            'NOT LOGGED IN' => [0, false],
            'General' => [1, false],
            'Wholesale' => [2, true],
            'Retailer' => [3, true]
        ];
    }

    /**
     * Verify that the group with the specified Id can or cannot be deleted.
     */
    public function testCanDeleteNoSuchGroup()
    {
        /* This group ID should not exist in the store. */
        $groupId = 9999;

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$groupId/permissions",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1CanDelete'
            ]
        ];

        $requestData = [CustomerGroup::ID => $groupId];

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
            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "Exception does not contain expected message."
            );
            $this->assertContains((string)$groupId, $e->getMessage());
        }
    }

    /**
     * Verify that creating a new group works via REST.
     */
    public function testCreateGroupRest()
    {
        $this->_markTestAsRestOnly();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ]
        ];

        $groupData = [
            CustomerGroup::ID => null,
            CustomerGroup::CODE => 'Create Group REST',
            CustomerGroup::TAX_CLASS_ID => 3
        ];
        $requestData = ['group' => $groupData];

        $groupId = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotNull($groupId);

        $newGroup = $this->groupService->getGroup($groupId);
        $this->assertEquals($groupId, $newGroup->getId(), 'The group id does not match.');
        $this->assertEquals($groupData[CustomerGroup::CODE], $newGroup->getCode(), 'The group code does not match.');
        $this->assertEquals(
            $groupData[CustomerGroup::TAX_CLASS_ID],
            $newGroup->getTaxClassId(),
            'The group tax class id does not match.'
        );
    }

    /**
     * Verify that creating a new group with a duplicate group name fails with an error via REST.
     */
    public function testCreateGroupDuplicateGroupRest()
    {
        $builder = Bootstrap::getObjectManager()->create('\Magento\Customer\Service\V1\Data\CustomerGroupBuilder');
        $this->_markTestAsRestOnly();

        $duplicateGroupCode = 'Duplicate Group Code REST';

        $this->createGroup(
            $builder->populateWithArray([
                CustomerGroup::ID => null,
                CustomerGroup::CODE => $duplicateGroupCode,
                CustomerGroup::TAX_CLASS_ID => 3
            ])->create()
        );

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ]
        ];

        $groupData = [
            CustomerGroup::ID => null,
            CustomerGroup::CODE => $duplicateGroupCode,
            CustomerGroup::TAX_CLASS_ID => 3
        ];
        $requestData = ['group' => $groupData];

        try {
            $this->_webApiCall($serviceInfo, $requestData);
            $this->fail("Expected exception");
        } catch (\Exception $e) {
            $errorData = json_decode($e->getMessage(), true);

            $this->assertEquals(
                'Customer Group already exists.',
                $errorData['message']
            );
            $this->assertEquals(400, $e->getCode(), 'Invalid HTTP code');
        }
    }

    /**
     * Verify that creating a new group works via REST if tax class id is empty, defaults 3.
     */
    public function testCreateGroupDefaultTaxClassIdRest()
    {
        $this->_markTestAsRestOnly();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ]
        ];

        $groupData = [
            CustomerGroup::ID => null,
            CustomerGroup::CODE => 'Default Class Tax ID REST',
            CustomerGroup::TAX_CLASS_ID => null
        ];
        $requestData = ['group' => $groupData];

        $groupId = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotNull($groupId);

        $newGroup = $this->groupService->getGroup($groupId);
        $this->assertEquals($groupId, $newGroup->getId(), 'The group id does not match.');
        $this->assertEquals($groupData[CustomerGroup::CODE], $newGroup->getCode(), 'The group code does not match.');
        $this->assertEquals(
            CustomerGroupService::DEFAULT_TAX_CLASS_ID,
            $newGroup->getTaxClassId(),
            'The group tax class id does not match.'
        );
    }

    /**
     * Verify that creating a new group without a code fails with an error.
     */
    public function testCreateGroupNoCodeExpectExceptionRest()
    {
        $this->_markTestAsRestOnly();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ]
        ];

        $groupData = [
            CustomerGroup::ID => null,
            CustomerGroup::CODE => null,
            CustomerGroup::TAX_CLASS_ID => null
        ];
        $requestData = ['group' => $groupData];

        try {
            $this->_webApiCall($serviceInfo, $requestData);
            $this->fail("Expected exception");
        } catch (\Exception $e) {
            // @codingStandardsIgnoreStart
            $this->assertContains(
                '{"message":"Invalid value of \"%value\" provided for the %fieldName field.","parameters":{"fieldName":"code","value":null}',
                $e->getMessage(),
                "Exception does not contain expected message."
            );
            // @codingStandardsIgnoreEnd
        }
    }

    /**
     * Verify that creating a new group with an invalid tax class id fails with an error.
     */
    public function testCreateGroupInvalidTaxClassIdRest()
    {
        $this->_markTestAsRestOnly();

        $invalidTaxClassId = 9999;

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ]
        ];

        $groupData = [
            CustomerGroup::ID => null,
            CustomerGroup::CODE => 'Invalid Tax Class Id Code',
            CustomerGroup::TAX_CLASS_ID => $invalidTaxClassId
        ];
        $requestData = ['group' => $groupData];

        try {
            $this->_webApiCall($serviceInfo, $requestData);
            $this->fail("Expected exception");
        } catch (\Exception $e) {
            // @codingStandardsIgnoreStart
            $this->assertContains(
                '{"message":"Invalid value of \"%value\" provided for the %fieldName field.","parameters":{"fieldName":"taxClassId","value":9999}',
                $e->getMessage(),
                "Exception does not contain expected message."
            );
            // codingStandardsIgnoreEnd
        }
    }

    /**
     * Verify that an attempt to update via POST is not allowed.
     */
    public function testCreateGroupWithIdRest()
    {
        $this->_markTestAsRestOnly();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ]
        ];

        $groupData = [
            CustomerGroup::ID => 88,
            CustomerGroup::CODE => 'Create Group With Id REST',
            CustomerGroup::TAX_CLASS_ID => 3
        ];
        $requestData = ['group' => $groupData];

        try {
            $this->_webApiCall($serviceInfo, $requestData);
            $this->fail('Expected exception');
        } catch (\Exception $e) {
            $this->assertContains(
                "ID is not expected for this request.",
                $e->getMessage(),
                "Exception does not contain expected message."
            );
        }
    }

    /**
     * Verify that creating a new group fails via SOAP if there is an Id specified.
     */
    public function testCreateGroupWithIdSoap()
    {
        $this->_markTestAsSoapOnly();

        $serviceInfo = [
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1CreateGroup'
            ]
        ];

        $groupData = [
            CustomerGroup::ID => 88,
            CustomerGroup::CODE => 'Create Group with Id SOAP',
            CustomerGroup::TAX_CLASS_ID => 3
        ];
        $requestData = ['group' => $groupData];

        try {
            $this->_webApiCall($serviceInfo, $requestData);
            $this->fail("Expected exception");
        } catch (\SoapFault $e) {
            $this->assertContains(
                "ID is not expected for this request.",
                $e->getMessage(),
                "SoapFault does not contain expected message."
            );
        }
    }

    /**
     * Verify that updating an existing group works via REST.
     */
    public function testUpdateGroupRest()
    {
        $this->_markTestAsRestOnly();
        $builder = Bootstrap::getObjectManager()->create('\Magento\Customer\Service\V1\Data\CustomerGroupBuilder');
        $groupId = $this->createGroup(
            $builder->populateWithArray([
                CustomerGroup::ID => null,
                CustomerGroup::CODE => 'New Group REST',
                CustomerGroup::TAX_CLASS_ID => 3
            ])->create()
        );

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$groupId",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ]
        ];

        $groupData = [
            CustomerGroup::ID => $groupId,
            CustomerGroup::CODE => 'Updated Group REST',
            CustomerGroup::TAX_CLASS_ID => 3
        ];
        $requestData = ['group' => $groupData];

        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));

        $group = $this->groupService->getGroup($groupId);
        $this->assertEquals($groupData[CustomerGroup::CODE], $group->getCode(), 'The group code did not change.');
        $this->assertEquals(
            $groupData[CustomerGroup::TAX_CLASS_ID],
            $group->getTaxClassId(),
            'The group tax class id did not change'
        );
    }

    /**
     * Verify that updating a non-existing group throws an exception.
     */
    public function testUpdateGroupNotExistingGroupRest()
    {
        $this->_markTestAsRestOnly();

        $nonExistentGroupId = '9999';

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$nonExistentGroupId",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ]
        ];

        $groupData = [
            CustomerGroup::ID => $nonExistentGroupId,
            CustomerGroup::CODE => 'Updated Group REST Does Not Exist',
            CustomerGroup::TAX_CLASS_ID => 3
        ];
        $requestData = ['group' => $groupData];

        try {
            $this->_webApiCall($serviceInfo, $requestData);
            $this->fail('Expected exception');
        } catch (\Exception $e) {
            $expectedMessage = '{"message":"No such entity with %fieldName = %fieldValue",'
             . '"parameters":{"fieldName":"id","fieldValue":"9999"}';
            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "Exception does not contain expected message."
            );
        }
    }

    /**
     * Verify that creating a new group works via SOAP.
     */
    public function testCreateGroupSoap()
    {
        $this->_markTestAsSoapOnly();

        $serviceInfo = [
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1CreateGroup'
            ]
        ];

        $groupData = [
            CustomerGroup::ID => null,
            CustomerGroup::CODE => 'Create Group SOAP',
            'taxClassId' => 3
        ];
        $requestData = ['group' => $groupData];

        $groupId = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotNull($groupId);

        $newGroup = $this->groupService->getGroup($groupId);
        $this->assertEquals($groupId, $newGroup->getId(), "The group id does not match.");
        $this->assertEquals($groupData[CustomerGroup::CODE], $newGroup->getCode(), "The group code does not match.");
        $this->assertEquals(
            $groupData['taxClassId'],
            $newGroup->getTaxClassId(),
            "The group tax class id does not match."
        );
    }

    /**
     * Verify that creating a new group with a duplicate code fails with an error via SOAP.
     */
    public function testCreateGroupDuplicateGroupSoap()
    {
        $this->_markTestAsSoapOnly();
        $builder = Bootstrap::getObjectManager()->create('\Magento\Customer\Service\V1\Data\CustomerGroupBuilder');
        $duplicateGroupCode = 'Duplicate Group Code SOAP';

        $this->createGroup(
            $builder->populateWithArray([
                CustomerGroup::ID => null,
                CustomerGroup::CODE => $duplicateGroupCode,
                CustomerGroup::TAX_CLASS_ID => 3
            ])->create()
        );

        $serviceInfo = [
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1CreateGroup'
            ]
        ];

        $groupData = [
            CustomerGroup::ID => null,
            CustomerGroup::CODE => $duplicateGroupCode,
            'taxClassId' => 3
        ];
        $requestData = ['group' => $groupData];

        $expectedMessage = 'Customer Group already exists.';

        try {
            $this->_webApiCall($serviceInfo, $requestData);
            $this->fail("Expected exception");
        } catch (\SoapFault $e) {
            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "Exception does not contain expected message."
            );
        }
    }

    /**
     * Verify that creating a new group works via SOAP if tax class id is empty, defaults 3.
     */
    public function testCreateGroupDefaultTaxClassIdSoap()
    {
        $this->_markTestAsSoapOnly();

        $serviceInfo = [
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1CreateGroup'
            ]
        ];

        $groupData = [
            CustomerGroup::ID => null,
            CustomerGroup::CODE => 'Default Class Tax ID SOAP',
            'taxClassId' => null
        ];
        $requestData = ['group' => $groupData];

        $groupId = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotNull($groupId);

        $newGroup = $this->groupService->getGroup($groupId);
        $this->assertEquals($groupId, $newGroup->getId(), "The group id does not match.");
        $this->assertEquals($groupData[CustomerGroup::CODE], $newGroup->getCode(), "The group code does not match.");
        $this->assertEquals(
            CustomerGroupService::DEFAULT_TAX_CLASS_ID,
            $newGroup->getTaxClassId(),
            "The group tax class id does not match."
        );
    }

    /**
     * Verify that creating a new group without a code fails with an error.
     */
    public function testCreateGroupNoCodeExpectExceptionSoap()
    {
        $this->_markTestAsSoapOnly();

        $serviceInfo = [
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1CreateGroup'
            ]
        ];

        $groupData = [
            CustomerGroup::ID => null,
            CustomerGroup::CODE => null,
            'taxClassId' => null
        ];
        $requestData = ['group' => $groupData];

        $expectedMessage ='Invalid value of "%value" provided for the %fieldName field.';

        try {
            $this->_webApiCall($serviceInfo, $requestData);
            $this->fail("Expected exception");
        } catch (\SoapFault $e) {
            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "SoapFault does not contain expected message."
            );
        }
    }

    /**
     * Verify that creating a new group fails via SOAP if tax class id is invalid.
     */
    public function testCreateGroupInvalidTaxClassIdSoap()
    {
        $this->_markTestAsSoapOnly();

        $invalidTaxClassId = 9999;

        $serviceInfo = [
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1CreateGroup'
            ]
        ];

        $groupData = [
            CustomerGroup::ID => null,
            CustomerGroup::CODE => 'Invalid Class Tax ID SOAP',
            'taxClassId' => $invalidTaxClassId
        ];
        $requestData = ['group' => $groupData];

        $expectedMessage = 'Invalid value of "%value" provided for the %fieldName field.';

        try {
            $this->_webApiCall($serviceInfo, $requestData);
            $this->fail("Expected exception");
        } catch (\SoapFault $e) {
            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "SoapFault does not contain expected message."
            );
        }
    }

    /**
     * Verify that updating an existing group works via SOAP.
     */
    public function testUpdateGroupSoap()
    {
        $this->_markTestAsSoapOnly();
        $builder = Bootstrap::getObjectManager()->create('\Magento\Customer\Service\V1\Data\CustomerGroupBuilder');
        $groupId = $this->createGroup(
            $builder->populateWithArray([
                    CustomerGroup::ID => null,
                    CustomerGroup::CODE => 'New Group SOAP',
                    CustomerGroup::TAX_CLASS_ID => 3
                ])->create()
        );

        $serviceInfo = [
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1UpdateGroup'
            ]
        ];

        $groupData = [
            CustomerGroup::ID => $groupId,
            CustomerGroup::CODE => 'Updated Group SOAP',
            'taxClassId' => 3
        ];
        $requestData = [CustomerGroup::ID => $groupId, 'group' => $groupData];

        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));

        $group = $this->groupService->getGroup($groupId);
        $this->assertEquals($groupData[CustomerGroup::CODE], $group->getCode(), 'The group code did not change.');
        $this->assertEquals(
            $groupData['taxClassId'],
            $group->getTaxClassId(),
            'The group tax class id did not change'
        );
    }

    /**
     * Verify that updating a non-existing group throws an exception via SOAP.
     */
    public function testUpdateGroupNotExistingGroupSoap()
    {
        $this->_markTestAsSoapOnly();

        $nonExistentGroupId = '9999';

        $serviceInfo = [
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1UpdateGroup'
            ]
        ];

        $groupData = [
            CustomerGroup::ID => $nonExistentGroupId,
            CustomerGroup::CODE => 'Updated Non-Existent Group SOAP',
            'taxClassId' => 3
        ];
        $requestData = [CustomerGroup::ID => $nonExistentGroupId, 'group' => $groupData];

        try {
            $this->_webApiCall($serviceInfo, $requestData);
        } catch (\Exception $e) {
            $expectedMessage = 'No such entity with %fieldName = %fieldValue';

            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "Exception does not contain expected message."
            );
        }
    }

    /**
     * Verify that deleting an existing group works.
     */
    public function testDeleteGroupExists()
    {
        $builder = Bootstrap::getObjectManager()->create('\Magento\Customer\Service\V1\Data\CustomerGroupBuilder');
        $groupId = $this->createGroup(
            $builder->populateWithArray([
                CustomerGroup::ID => null,
                CustomerGroup::CODE => 'Delete Group',
                CustomerGroup::TAX_CLASS_ID => 3
            ])->create()
        );

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$groupId",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1DeleteGroup'
            ]
        ];

        $requestData = [CustomerGroup::ID => $groupId];
        $response = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue($response, 'Expected response should be true.');

        try {
            $this->groupService->getGroup($groupId);
            $this->fail('An expected NoSuchEntityException was not thrown.');
        } catch (NoSuchEntityException $e) {
            $exception = NoSuchEntityException::singleField(CustomerGroup::ID, $groupId);
            $this->assertEquals(
                $exception->getMessage(),
                $e->getMessage(),
                'Exception message does not match expected message.'
            );
        }
    }

    /**
     * Verify that deleting an non-existing group works.
     */
    public function testDeleteGroupNotExists()
    {
        $groupId = 4200;

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$groupId",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1DeleteGroup'
            ]
        ];

        $requestData = array(CustomerGroup::ID => $groupId);
        $expectedMessage = NoSuchEntityException::MESSAGE_SINGLE_FIELD;
        $expectedParameters = ['fieldName' => CustomerGroup::ID, 'fieldValue' => $groupId];

        try {
            $this->_webApiCall($serviceInfo, $requestData);
        } catch (\SoapFault $e) {
            $this->assertContains($expectedMessage, $e->getMessage(), "SoapFault does not contain expected message.");
        } catch (\Exception $e) {
            $errorObj = $this->processRestExceptionResult($e);
            $this->assertEquals($expectedMessage, $errorObj['message']);
            $this->assertEquals($expectedParameters, $errorObj['parameters']);
        }
    }

    /**
     * Verify that the group with the specified Id cannot be deleted because it is the default group and a proper
     * fault is returned.
     */
    public function testDeleteGroupCannotDelete()
    {
        $groupIdAssignedDefault = 1;

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$groupIdAssignedDefault",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1DeleteGroup'
            ]
        ];

        $requestData = [CustomerGroup::ID => $groupIdAssignedDefault];
        $expectedMessage = "Cannot delete group.";

        try {
            $this->_webApiCall($serviceInfo, $requestData);
            $this->fail("Expected exception");
        } catch (\SoapFault $e) {
            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "SoapFault does not contain expected message."
            );
        } catch (\Exception $e) {
            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "Exception does not contain expected message."
            );
        }

        $this->assertNotNull($this->groupService->getGroup($groupIdAssignedDefault));
    }

    /**
     * Create a test group.
     *
     * @param CustomerGroup $group The group to create and save.
     * @return int The group Id of the group that was created.
     */
    private function createGroup($group)
    {
        $groupId = $this->groupService->createGroup($group);
        $this->assertNotNull($groupId);

        $newGroup = $this->groupService->getGroup($groupId);
        $this->assertEquals($groupId, $newGroup->getId(), 'The group id does not match.');
        $this->assertEquals($group->getCode(), $newGroup->getCode(), 'The group code does not match.');
        $this->assertEquals(
            $group->getTaxClassId(),
            $newGroup->getTaxClassId(),
            'The group tax class id does not match.'
        );

        $this->groupRegistry->remove($groupId);

        return $groupId;
    }

    /**
     * Data provider for testSearchGroups
     */
    public function testSearchGroupsDataProvider()
    {
        return [
            ['tax_class_id', '3', []],
            ['tax_class_id', '0', null],
            ['code', md5(mt_rand(0,10000000000) . time()), null],
            [
                'id',
                '0',
                [
                    'id' => '0',
                    'code' => 'NOT LOGGED IN',
                    'tax_class_id' => '3',
                    'tax_class_name' => 'Retail Customer'
                ]
            ],
            [
                'code',
                'General',
                [
                    'id' => '1',
                    'code' => 'General',
                    'tax_class_id' => '3',
                    'tax_class_name' => 'Retail Customer'
                ]
            ],
            [
                'id',
                '2',
                [
                    'id' => '2',
                    'code' => 'Wholesale',
                    'tax_class_id' => '3',
                    'tax_class_name' => 'Retail Customer'
                ]
            ],
            [
                'code',
                'Retailer',
                [
                    'id' => '3',
                    'code' => 'Retailer',
                    'tax_class_id' => '3',
                    'tax_class_name' => 'Retail Customer'
                ]
            ]
        ];
    }

    /**
     * Test search customer group
     *
     * @param string $filterField Customer Group field to filter by
     * @param string $filterValue Value of the field to be filtered by
     * @param array $expectedResult Expected search result
     *
     * @dataProvider testSearchGroupsDataProvider
     */
    public function testSearchGroups($filterField, $filterValue, $expectedResult)
    {
        $filterBuilder = Bootstrap::getObjectManager()->create('Magento\Framework\Service\V1\Data\FilterBuilder');
        $searchCriteriaBuilder =  Bootstrap::getObjectManager()
            ->create('Magento\Framework\Service\V1\Data\SearchCriteriaBuilder');
        $filter = $filterBuilder
                    ->setField($filterField)
                    ->setValue($filterValue)
                    ->create();
        $searchCriteriaBuilder->addFilter([$filter]);

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/search",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1SearchGroups'
            ]
        ];

        $searchData = $searchCriteriaBuilder->create()->__toArray();
        $requestData = ['searchCriteria' => $searchData];

        $searchResult = $this->_webApiCall($serviceInfo, $requestData);

        if (is_null($expectedResult)) {
            $this->assertEquals(0, $searchResult['total_count']);
        } elseif(is_array($expectedResult))  {
            $this->assertGreaterThan(0, $searchResult['total_count']);
            if(!empty($expectedResult)) {
                $this->assertEquals($expectedResult, $searchResult['items'][0]);
            }
        }
    }

    /**
     * Test search customer group using GET
     *
     * @param string $filterField Customer Group field to filter by
     * @param string $filterValue Value of the field to be filtered by
     * @param array $expectedResult Expected search result
     *
     * @dataProvider testSearchGroupsDataProvider
     */
    public function testSearchGroupsWithGET($filterField, $filterValue, $expectedResult)
    {
        $this->_markTestAsRestOnly('SOAP is covered in ');
        $filterBuilder = Bootstrap::getObjectManager()->create('Magento\Framework\Service\V1\Data\FilterBuilder');
        $searchCriteriaBuilder =  Bootstrap::getObjectManager()
            ->create('Magento\Framework\Service\V1\Data\SearchCriteriaBuilder');
        $filter = $filterBuilder
            ->setField($filterField)
            ->setValue($filterValue)
            ->create();
        $searchCriteriaBuilder->addFilter([$filter]);
        $searchData = $searchCriteriaBuilder->create()->__toArray();
        $requestData = ['searchCriteria' => $searchData];
        $searchQueryString = http_build_query($requestData);
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/search?' . $searchQueryString,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ]
        ];
        $searchResult = $this->_webApiCall($serviceInfo);

        if (is_null($expectedResult)) {
            $this->assertEquals(0, $searchResult['total_count']);
        } elseif(is_array($expectedResult))  {
            $this->assertGreaterThan(0, $searchResult['total_count']);
            if(!empty($expectedResult)) {
                $this->assertEquals($expectedResult, $searchResult['items'][0]);
            }
        }
    }
}
