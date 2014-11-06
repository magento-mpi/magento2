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

class ProductAttributeGroupRepositoryTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductAttributeReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/attribute-sets';

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/empty_attribute_group.php
     */
    public function testCreateGroup()
    {
        $attributeSetId = 1;
        $groupData = $this->createGroupData($attributeSetId);
        $groupData['name'] = 'empty_attribute_group_updated';

        $result = $this->createGroup($attributeSetId, $groupData);
        $this->assertArrayHasKey('id', $result);
        $this->assertNotNull($result['id']);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/empty_attribute_group.php
     */
    public function testDeleteGroup()
    {
        $group = $this->getGroupByName('empty_attribute_group');

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/groups/" . $group->getId(),
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'catalogProductAttributeGroupWriteServiceV1Delete'
            ]
        ];
        $this->assertTrue($this->_webApiCall($serviceInfo));
    }

    /**
     * @expectedException \Exception
     */
    public function testCreateGroupWithAttributeSetThatDoesNotExist()
    {
        $attributeSetId = -1;
        $this->createGroup($attributeSetId);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/empty_attribute_group.php
     */
    public function testUpdateGroup()
    {
        $attributeSetId = 1;
        $group = $this->getGroupByName('empty_attribute_group');

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
        $newGroupData['name'] = 'empty_attribute_group_updated';
        $newGroupData['id'] = $group->getId();

        $result = $this->_webApiCall($serviceInfo, ['group' => $newGroupData]);

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals($group->getId(), $result['id']);
        $this->assertArrayHasKey('name', $result);
        $this->assertEquals($newGroupData['name'], $result['name']);
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
    protected function createGroup($attributeSetId, $groupData = null)
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
        return $this->_webApiCall(
            $serviceInfo,
            ['group' => $groupData ? $groupData : $this->createGroupData($attributeSetId)]
        );
    }

    /**
     * @param $attributeSetId
     * @return array
     */
    protected function createGroupData($attributeSetId)
    {
        return [
            'name' => 'empty_attribute_group',
            'attribute_set_id' => $attributeSetId
        ];
    }

    /**
     * Retrieve attribute group based on given name.
     * This utility methods assumes that there is only one attribute group with given name,
     *
     * @param string $groupName
     * @return \Magento\Eav\Model\Entity\Attribute\Group|null
     */
    protected function getGroupByName($groupName)
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Eav\Model\Entity\Attribute\Group */
        $attributeGroup = $objectManager->create('\Magento\Eav\Model\Entity\Attribute\Group')
            ->load($groupName, 'attribute_group_name');
        if ($attributeGroup->getId() === null) {
            return null;
        }
        return $attributeGroup;
    }
}
