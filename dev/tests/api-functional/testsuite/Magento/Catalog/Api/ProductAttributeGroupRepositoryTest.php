<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api;

use \Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\TestFramework\Helper\Bootstrap;

class ProductAttributeGroupRepositoryTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductAttributeReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/attribute-sets';

    /**
     * @var \Magento\Eav\Model\Attribute\GroupRepository
     */
    protected $groupRepository;

    protected function setUp()
    {
        $this->groupRepository = Bootstrap::getObjectManager()->get('\Magento\Eav\Model\Attribute\GroupRepository');
    }

    public function testCreateGroup()
    {
        $attributeSetId = 1;
        $result = $this->createGroup($attributeSetId);
        $this->assertArrayHasKey('id', $result);
        $this->assertNotNull($result['id']);

        $this->groupRepository->deleteById($result['id']);
    }

    public function testDeleteGroup()
    {
        $attributeSetId = 1;
        $result = $this->createGroup($attributeSetId);
        $this->assertArrayHasKey('id', $result);
        $this->assertNotNull($result['id']);

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/groups/" . $result['id'],
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'catalogProductAttributeGroupWriteServiceV1Delete'
            ]
        ];
        return $this->_webApiCall($serviceInfo);
    }

    /**
     * @expectedException \Exception
     */
    public function testCreateGroupWithAttributeSetThatDoesNotExist()
    {
        $attributeSetId = -1;
        $this->createGroup($attributeSetId);
    }

    public function testUpdateGroup()
    {
        $attributeSetId = 1;
        /** @var \Magento\Eav\Api\Data\AttributeGroupInterfaceDataBuilder $builder */
        $builder = Bootstrap::getObjectManager()->get('\Magento\Eav\Api\Data\AttributeGroupInterfaceDataBuilder');
        $builder->setName('OldGroupName');
        $builder->setAttributeSetId($attributeSetId);
        $group = $this->groupRepository->save($builder->create());

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $attributeSetId . '/groups',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Save'
            ],
        ];

        $newGroupData = $this->createGroupData($attributeSetId);
        $newGroupData['id'] = $group->getId();
        $result = $this->_webApiCall($serviceInfo, ['group' => $newGroupData]);

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals($group->getId(), $result['id']);
        $this->assertArrayHasKey('name', $result);
        $this->assertEquals($newGroupData['name'], $result['name']);

        $this->groupRepository->deleteById($group->getId());
    }

    public function testGetList()
    {
        $this->markTestIncomplete('Need new searchResult/searchCriteria');
        $attributeSetId = 1;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/" . $attributeSetId . "/groups",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
        ];

        $groups = $this->_webApiCall($serviceInfo);
    }

    /**
     * @param $attributeSetId
     * @return array|bool|float|int|string
     */
    protected function createGroup($attributeSetId)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/groups',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Save'
            ],
        ];
        return $this->_webApiCall($serviceInfo, ['group' => $this->createGroupData($attributeSetId)]);
    }

    /**
     * @param $attributeSetId
     * @return array
     */
    protected function createGroupData($attributeSetId)
    {
        return [
            'name' => 'NewGroupName',
            'attribute_set_id' => $attributeSetId
        ];
    }
}
