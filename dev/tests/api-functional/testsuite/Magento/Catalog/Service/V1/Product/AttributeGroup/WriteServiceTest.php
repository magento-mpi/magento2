<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\AttributeGroup;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

class WriteServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductAttributeGroupWriteServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/attribute-sets';

    protected function _getGroups($attributeSetId)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/" . $attributeSetId . "/groups",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
        ];

        return $this->_webApiCall($serviceInfo, array(), self::ADAPTER_REST);
    }

    public function testCreate()
    {
        $groupCountBefore = count($this->_getGroups(1));

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/1/groups",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME .  'Create'
            ]
        ];

        $this->_webApiCall(
            $serviceInfo,
            ['attributeSetId' => 1, 'groupData' => ['id' => null, 'name' => 'New Group']]
        );

        $groupData = $this->_getGroups(1);
        $groupCountAfter = count($groupData);

        $newGroup = $groupData[$groupCountAfter-1];
        $groupService = Bootstrap::getObjectManager()
            ->get('Magento\Catalog\Service\V1\Product\AttributeGroup\WriteServiceInterface');
        $groupService->delete($newGroup['id']);

        $this->assertCount($groupCountBefore + 1, $groupData, "The group data does not match.");
        $this->assertEquals('New Group', $newGroup['name'], "The group data does not match.");
    }

    public function testUpdate()
    {
        /** @var \Magento\Catalog\Service\V1\Product\AttributeGroup\WriteServiceInterface $groupService */
        $groupService = Bootstrap::getObjectManager()
            ->get('Magento\Catalog\Service\V1\Product\AttributeGroup\WriteServiceInterface');
        /** @var \Magento\Catalog\Service\V1\Product\AttributeGroup\ReadServiceInterface $groupReadService */
        $groupReadService = Bootstrap::getObjectManager()
            ->get('Magento\Catalog\Service\V1\Product\AttributeGroup\ReadServiceInterface');
        $builder = Bootstrap::getObjectManager()->get(
            '\Magento\Catalog\Service\V1\Data\Eav\AttributeGroupBuilder'
        );
        $group = $builder->setName('GroupToUpdate')->create();
        $group = $groupService->create(1, $group);
        $groups = $groupReadService->getList(1);
        $this->assertEquals('GroupToUpdate', $groups[count($groups) - 1]->getName());
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/1/groups",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'catalogProductAttributeGroupWriteServiceV1Update'
            ]
        ];
        $this->_webApiCall(
            $serviceInfo,
            ['groupId' => $group->getId(), 'groupData' => ['id' => $group->getId(), 'name' => 'UpdatedGroup']]
        );
        $groups = $this->_getGroups(1);

        $lastGroup = end($groups);
        $this->assertEquals($group->getId(), $lastGroup['id']);
        $this->assertEquals('UpdatedGroup', $lastGroup['name']);
        $groupService->delete($lastGroup['id']);
    }

    public function testDelete()
    {
        /** @var \Magento\Catalog\Service\V1\Product\AttributeGroup\WriteServiceInterface $groupService */
        $groupService = Bootstrap::getObjectManager()
            ->get('Magento\Catalog\Service\V1\Product\AttributeGroup\WriteServiceInterface');
        /** @var \Magento\Catalog\Service\V1\Product\AttributeGroup\ReadServiceInterface $groupReadService */
        $groupReadService = Bootstrap::getObjectManager()
            ->get('Magento\Catalog\Service\V1\Product\AttributeGroup\ReadServiceInterface');
        $builder = Bootstrap::getObjectManager()->get(
            '\Magento\Catalog\Service\V1\Data\Eav\AttributeGroupBuilder'
        );
        $group = $builder->setName('GroupToDelete')->create();
        $group = $groupService->create(1, $group);
        $groups = $groupReadService->getList(1);
        $groupCount = count($groups);
        $this->assertEquals('GroupToDelete', $groups[count($groups) - 1]->getName());

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/1/groups/" . $group->getId(),
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'catalogProductAttributeGroupWriteServiceV1Delete'
            ]
        ];

        $this->_webApiCall($serviceInfo, ['groupId' => $group->getId()]);
        $groups = $this->_getGroups(1);
        $lastGroup = end($groups);
        $this->assertEquals($groupCount - 1, count($groups));
        $this->assertNotEquals('GroupToDelete', $lastGroup['name'], "Group was not removed");
    }
} 
