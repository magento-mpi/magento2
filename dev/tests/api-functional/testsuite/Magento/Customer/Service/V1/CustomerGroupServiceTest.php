<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1;

use Magento\Customer\Service\V1\Data\CustomerGroup;
use Magento\Customer\Service\V1\Data\CustomerGroupBuilder;
use Magento\Customer\Service\V1\CustomerGroupService;
use Magento\Customer\Service\V1\CustomerGroupServiceInterface;
use Magento\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class CustomerGroupServiceTest
 */
class CustomerGroupServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = "customerCustomerGroupServiceV1";
    const SERVICE_VERSION = "V1";
    const RESOURCE_PATH = "/V1/customerGroup";

    /**
     * @var CustomerGroupServiceInterface
     */
    private $groupService;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->groupService = $objectManager->get('Magento\Customer\Service\V1\CustomerGroupServiceInterface');
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
        $groupId = $testGroup['id'];
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
        $requestData = ['groupId' => $groupId];
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
            'NOT LOGGED IN' => [['id' => 0, 'code' => 'NOT LOGGED IN', 'tax_class_id' => 3]],
            'General' => [['id' => 1, 'code' => 'General', 'tax_class_id' => 3]],
            'Wholesale' => [['id' => 2, 'code' => 'Wholesale', 'tax_class_id' => 3]],
            'Retailer' => [['id' => 3, 'code' => 'Retailer', 'tax_class_id' => 3]],
        ];
    }

    /**
     * Verify the retrieval of all customer groups.
     */
    public function testGetGroups()
    {
        $expectedGroups = [
            ['id' => 0, 'code' => 'NOT LOGGED IN', 'tax_class_id' => 3],
            ['id' => 1, 'code' => 'General', 'tax_class_id' => 3],
            ['id' => 2, 'code' => 'Wholesale', 'tax_class_id' => 3],
            ['id' => 3, 'code' => 'Retailer', 'tax_class_id' => 3]
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
            'admin' => [0, ['id' => 1, 'code' => 'General', 'tax_class_id' => 3]],
            'base' => [1, ['id' => 1, 'code' => 'General', 'tax_class_id' => 3]]
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
        $expectedMessage = "No such entity with storeId = $nonExistentStoreId";

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
                'resourcePath' => self::RESOURCE_PATH . "/canDelete/$groupId",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1CanDelete'
            ]
        ];

        $requestData = ['groupId' => $groupId];

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
                'resourcePath' => self::RESOURCE_PATH . "/canDelete/$groupId",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1CanDelete'
            ]
        ];

        $requestData = ['groupId' => $groupId];

        $expectedMessage = "No such entity with groupId = $groupId";

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
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ]
        ];

        $groupData = [
            'id' => null,
            'code' => 'Create Group REST',
            'tax_class_id' => 3
        ];
        $requestData = ['group' => $groupData];

        $groupId = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotNull($groupId);

        $newGroup = $this->groupService->getGroup($groupId);
        $this->assertEquals($groupId, $newGroup->getId(), 'The group id does not match.');
        $this->assertEquals($groupData['code'], $newGroup->getCode(), 'The group code does not match.');
        $this->assertEquals(
            $groupData['tax_class_id'],
            $newGroup->getTaxClassId(),
            'The group tax class id does not match.'
        );
    }

    /**
     * Verify that creating a new group with a duplicate group name fails with an error via REST.
     */
    public function testCreateGroupDuplicateGroupRest()
    {
        $this->_markTestAsRestOnly();

        $duplicateGroupCode = 'Duplicate Group Code REST';

        $groupId = $this->createGroup(
            (new CustomerGroupBuilder())->populateWithArray([
                'id' => null,
                'code' => $duplicateGroupCode,
                'tax_class_id' => 3
            ])->create()
        );

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ]
        ];

        $groupData = [
            'id' => null,
            'code' => $duplicateGroupCode,
            'tax_class_id' => 3
        ];
        $requestData = ['group' => $groupData];

        try {
            $this->_webApiCall($serviceInfo, $requestData);
            $this->fail("Expected exception");
        } catch (\Exception $e) {
            $errorData = json_decode($e->getMessage(), true);

            $this->assertTrue(
                isset($errorData['errors'][0]['message']),
                'Invalid error message format: ' . $e->getMessage()
            );

            $this->assertCount(1, $errorData['errors']);
            $errorData = $errorData['errors'][0];

            $this->assertEquals('Customer Group already exists.', $errorData['message'], 'Invalid error message');
            $this->assertEquals(500, $errorData['http_code'], 'Invalid HTTP code');
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
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ]
        ];

        $groupData = [
            'id' => null,
            'code' => 'Default Class Tax ID REST',
            'tax_class_id' => null
        ];
        $requestData = ['group' => $groupData];

        $groupId = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotNull($groupId);

        $newGroup = $this->groupService->getGroup($groupId);
        $this->assertEquals($groupId, $newGroup->getId(), 'The group id does not match.');
        $this->assertEquals($groupData['code'], $newGroup->getCode(), 'The group code does not match.');
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
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ]
        ];

        $groupData = [
            'id' => null,
            'code' => null,
            'tax_class_id' => null
        ];
        $requestData = ['group' => $groupData];

        $expectedMessage = 'One or more input exceptions have occurred.\n'
            . '{\n'
            . '\tcode: INVALID_FIELD_VALUE\n'
            . '\tcode: \n'
            . '\tparams: []\n'
            . ' }';

        try {
            $this->_webApiCall($serviceInfo, $requestData);
            $this->fail("Expected exception");
        } catch (\Exception $e) {
            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "Exception does not contain expected message."
            );
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
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ]
        ];

        $groupData = [
            'id' => null,
            'code' => 'Invalid Tax Class Id Code',
            'tax_class_id' => $invalidTaxClassId
        ];
        $requestData = ['group' => $groupData];

        $expectedMessage = 'One or more input exceptions have occurred.\n'
            . '{\n'
            . '\tcode: INVALID_FIELD_VALUE\n'
            . '\ttaxClassId: ' . $invalidTaxClassId . '\n'
            . '\tparams: []\n'
            . ' }';

        try {
            $this->_webApiCall($serviceInfo, $requestData);
            $this->fail("Expected exception");
        } catch (\Exception $e) {
            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "Exception does not contain expected message."
            );
        }
    }

    /**
     * Verify that updating an existing group works via REST.
     */
    public function testUpdateGroupRest()
    {
        $this->_markTestAsRestOnly();

        $groupId = $this->createGroup(
            (new CustomerGroupBuilder())->populateWithArray([
                'id' => null,
                'code' => 'New Group REST',
                'tax_class_id' => 3
            ])->create()
        );

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ]
        ];

        $groupData = [
            'id' => $groupId,
            'code' => 'Updated Group REST',
            'tax_class_id' => 3
        ];
        $requestData = ['group' => $groupData];

        $newGroupId = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($groupId, $newGroupId, 'The group id should remain unchanged.');

        $group = $this->groupService->getGroup($newGroupId);
        $this->assertEquals($groupData['code'], $group->getCode(), 'The group code did not change.');
        $this->assertEquals(
            $groupData['tax_class_id'],
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

        $nonExistentGroupId = 9999;

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ]
        ];

        $groupData = [
            'id' => $nonExistentGroupId,
            'code' => 'Updated Group REST Does Not Exist',
            'tax_class_id' => 3
        ];
        $requestData = ['group' => $groupData];

        try {
            $this->_webApiCall($serviceInfo, $requestData);
            $this->fail("Expected exception");
        } catch (\Exception $e) {
            $expectedMessage = "No such entity with id = $nonExistentGroupId";

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
                'operation' => 'customerCustomerGroupServiceV1SaveGroup'
            ]
        ];

        $groupData = [
            'id' => null,
            'code' => 'Create Group SOAP',
            'taxClassId' => 3
        ];
        $requestData = ['group' => $groupData];

        $groupId = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotNull($groupId);

        $newGroup = $this->groupService->getGroup($groupId);
        $this->assertEquals($groupId, $newGroup->getId(), "The group id does not match.");
        $this->assertEquals($groupData['code'], $newGroup->getCode(), "The group code does not match.");
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

        $duplicateGroupCode = 'Duplicate Group Code SOAP';

        $groupId = $this->createGroup(
            (new CustomerGroupBuilder())->populateWithArray([
                'id' => null,
                'code' => $duplicateGroupCode,
                'tax_class_id' => 3
            ])->create()
        );

        $serviceInfo = [
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1SaveGroup'
            ]
        ];

        $groupData = [
            'id' => null,
            'code' => $duplicateGroupCode,
            'taxClassId' => 3
        ];
        $requestData = ['group' => $groupData];

        $expectedMessage = "Customer Group already exists.\n"
            . "{\n"
            . "\tcode: INVALID_FIELD_VALUE\n"
            . "\tcode: " . $duplicateGroupCode . "\n"
            . "\tparams: []\n"
            . ' }';

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
                'operation' => 'customerCustomerGroupServiceV1SaveGroup'
            ]
        ];

        $groupData = [
            'id' => null,
            'code' => 'Default Class Tax ID SOAP',
            'taxClassId' => null
        ];
        $requestData = ['group' => $groupData];

        $groupId = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotNull($groupId);

        $newGroup = $this->groupService->getGroup($groupId);
        $this->assertEquals($groupId, $newGroup->getId(), "The group id does not match.");
        $this->assertEquals($groupData['code'], $newGroup->getCode(), "The group code does not match.");
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
                'operation' => 'customerCustomerGroupServiceV1SaveGroup'
            ]
        ];

        $groupData = [
            'id' => null,
            'code' => null,
            'taxClassId' => null
        ];
        $requestData = ['group' => $groupData];

        $expectedMessage = "One or more input exceptions have occurred.\n"
            . "{\n"
            . "\tcode: INVALID_FIELD_VALUE\n"
            . "\tcode: \n"
            . "\tparams: []\n"
            . ' }';

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
                'operation' => 'customerCustomerGroupServiceV1SaveGroup'
            ]
        ];

        $groupData = [
            'id' => null,
            'code' => 'Invalid Class Tax ID SOAP',
            'taxClassId' => $invalidTaxClassId
        ];
        $requestData = ['group' => $groupData];

        $expectedMessage = "One or more input exceptions have occurred.\n"
            . "{\n"
            . "\tcode: INVALID_FIELD_VALUE\n"
            . "\ttaxClassId: " . $invalidTaxClassId . "\n"
            . "\tparams: []\n"
            . ' }';

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

        $groupId = $this->createGroup(
            (new CustomerGroupBuilder())->populateWithArray([
                    'id' => null,
                    'code' => 'New Group SOAP',
                    'tax_class_id' => 3
                ])->create()
        );

        $serviceInfo = [
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1SaveGroup'
            ]
        ];

        $groupData = [
            'id' => $groupId,
            'code' => 'Updated Group SOAP',
            'taxClassId' => 3
        ];
        $requestData = ['group' => $groupData];

        $newGroupId = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($groupId, $newGroupId, 'The group id should remain unchanged.');

        $group = $this->groupService->getGroup($newGroupId);
        $this->assertEquals($groupData['code'], $group->getCode(), 'The group code did not change.');
        $this->assertEquals(
            $groupData['taxClassId'],
            $group->getTaxClassId(),
            'The group tax class id did not change'
        );
    }

    /**
     * Verify that updating a non-existing group throws an exception  via SOAP.
     */
    public function testUpdateGroupNotExistingGroupSoap()
    {
        $this->_markTestAsSoapOnly();

        $nonExistentGroupId = 9999;

        $serviceInfo = [
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerGroupServiceV1SaveGroup'
            ]
        ];

        $groupData = [
            'id' => $nonExistentGroupId,
            'code' => 'Updated Non-Existent Group SOAP',
            'taxClassId' => 3
        ];
        $requestData = ['group' => $groupData];

        try {
            $this->_webApiCall($serviceInfo, $requestData);
        } catch (\Exception $e) {
            $expectedMessage = "No such entity with id = $nonExistentGroupId";

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
        $groupId = $this->createGroup(
            (new CustomerGroupBuilder())->populateWithArray([
                'id' => null,
                'code' => 'Delete Group',
                'tax_class_id' => 3
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

        $requestData = ['groupId' => $groupId];
        $response = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue($response, 'Expected response should be true.');

        try {
            $this->groupService->getGroup($groupId);
            $this->fail('An expected NoSuchEntityException was not thrown.');
        } catch (NoSuchEntityException $e) {
            $this->assertEquals(
                "No such entity with groupId = $groupId",
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

        $requestData = ['groupId' => $groupId];
        $expectedMessage = "No such entity with groupId = $groupId";

        try {
            $this->_webApiCall($serviceInfo, $requestData);
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

        $requestData = ['groupId' => $groupIdAssignedDefault];
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
        $groupId = $this->groupService->saveGroup($group);
        $this->assertNotNull($groupId);

        $newGroup = $this->groupService->getGroup($groupId);
        $this->assertEquals($groupId, $newGroup->getId(), 'The group id does not match.');
        $this->assertEquals($group->getCode(), $newGroup->getCode(), 'The group code does not match.');
        $this->assertEquals(
            $group->getTaxClassId(),
            $newGroup->getTaxClassId(),
            'The group tax class id does not match.'
        );

        return $groupId;
    }
}
