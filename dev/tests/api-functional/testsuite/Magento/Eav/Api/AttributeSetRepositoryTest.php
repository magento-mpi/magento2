<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

class AttributeSetRepositoryTest extends WebapiAbstract
{
    /**
     * @magentoApiDataFixture Magento/Eav/_files/empty_attribute_set.php
     */
    public function testGet()
    {
        $attributeSetName = 'empty_attribute_set';
        $attributeSet = $this->getAttributeSetByName($attributeSetName);
        $attributeSetId = $attributeSet->getId();

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/attribute-sets/' . $attributeSetId,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ),
            'soap' => array(
                // @todo fix SOAP configuration after SOAP tests are functional
                'operation' => '',
            ),
        );
        $arguments = array(
            'attributeSetId' => $attributeSetId,
        );
        $result = $this->_webApiCall($serviceInfo, $arguments);
        $this->assertNotNull($result);
        $this->assertEquals($attributeSet->getId(), $result['attribute_set_id']);
        $this->assertEquals($attributeSet->getAttributeSetName(), $result['attribute_set_name']);
        $this->assertEquals($attributeSet->getEntityTypeId(), $result['entity_type_id']);
        $this->assertEquals($attributeSet->getSortOrder(), $result['sort_order']);
    }

    /**
     * @expectedException \Exception
     */
    public function testGetThrowsExceptionIfRequestedAttributeSetDoesNotExist()
    {
        $attributeSetId = 9999;

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/attribute-sets/' . $attributeSetId,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ),
            'soap' => array(
                // @todo fix SOAP configuration after SOAP tests are functional
                'operation' => '',
            ),
        );
        $arguments = array(
            'attributeSetId' => $attributeSetId,
        );
        $this->_webApiCall($serviceInfo, $arguments);
    }

    /**
     * @magentoApiDataFixture Magento/Eav/_files/empty_attribute_set.php
     */
    public function testSave()
    {
        $attributeSetName = 'empty_attribute_set';
        $attributeSet = $this->getAttributeSetByName($attributeSetName);
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/attribute-sets',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ),
            'soap' => array(
                // @todo fix SOAP configuration after SOAP tests are functional
                'operation' => '',
            ),
        );

        $updatedSortOrder = $attributeSet->getSortOrder() + 200;

        $arguments = array(
            'attributeSet' => array(
                'attribute_set_id' => $attributeSet->getId(),
                // name is the same, because it is used by fixture rollback script
                'attribute_set_name' => $attributeSet->getAttributeSetName(),
                'entity_type_id' => $attributeSet->getEntityTypeId(),
                'sort_order' => $updatedSortOrder,
            ),
        );
        $result = $this->_webApiCall($serviceInfo, $arguments);
        $this->assertNotNull($result);
        // Reload attribute set data
        $attributeSet = $this->getAttributeSetByName($attributeSetName);
        $this->assertEquals($attributeSet->getAttributeSetId(), $result['attribute_set_id']);
        $this->assertEquals($attributeSet->getAttributeSetName(), $result['attribute_set_name']);
        $this->assertEquals($attributeSet->getEntityTypeId(), $result['entity_type_id']);
        $this->assertEquals($updatedSortOrder, $result['sort_order']);
        $this->assertEquals($attributeSet->getSortOrder(), $result['sort_order']);
    }

    /**
     * @magentoApiDataFixture Magento/Eav/_files/empty_attribute_set.php
     */
    public function testDeleteById()
    {
        $attributeSetName = 'empty_attribute_set';
        $attributeSet = $this->getAttributeSetByName($attributeSetName);
        $attributeSetId = $attributeSet->getId();

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/attribute-sets/' . $attributeSetId,
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ),
            'soap' => array(
                // @todo fix SOAP configuration after SOAP tests are functional
                'operation' => '',
            ),
        );

        $arguments = array(
            'attributeSetId' => $attributeSetId,
        );
        $this->assertTrue($this->_webApiCall($serviceInfo, $arguments));
        $this->assertNull($this->getAttributeSetByName($attributeSetName));
    }

    /**
     * @expectedException \Exception
     */
    public function testDeleteByIdThrowsExceptionIfRequestedAttributeSetDoesNotExist()
    {
        $attributeSetId = 9999;

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/attribute-sets/' . $attributeSetId,
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ),
            'soap' => array(
                // @todo fix SOAP configuration after SOAP tests are functional
                'operation' => '',
            ),
        );

        $arguments = array(
            'attributeSetId' => $attributeSetId,
        );
        $this->_webApiCall($serviceInfo, $arguments);
    }

    /**
     * @magentoApiDataFixture Magento/Eav/_files/empty_attribute_set.php
     */
    public function testGetList()
    {
        $searchCriteria = [
            'searchCriteria' => [
                'filter_groups' => [
                    [
                        'filters' => [
                            [
                                'field' => 'entity_type_code',
                                'value' => 'catalog_product',
                                'condition_type' => 'eq'
                            ]
                        ]
                    ],
                ],
                'current_page' => 1,
                'page_size' => 2
            ]
        ];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/attribute-sets/sets/list',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                // @todo fix SOAP configuration after SOAP tests are functional
                'operation' => '',
            ],
        ];

        $response = $this->_webApiCall($serviceInfo, $searchCriteria);

        $this->assertArrayHasKey('search_criteria', $response);
        $this->assertArrayHasKey('total_count', $response);
        $this->assertArrayHasKey('items', $response);

        $this->assertEquals($searchCriteria['searchCriteria'], $response['search_criteria']);
        $this->assertTrue($response['total_count'] > 0);
        $this->assertTrue(count($response['items']) > 0);

        $this->assertNotNull($response['items'][0]['attribute_set_id']);
        $this->assertNotNull($response['items'][0]['attribute_set_name']);
    }

    /**
     * Retrieve attribute set based on given name.
     * This utility methods assumes that there is only one attribute set with given name,
     *
     * @param string $attributeSetName
     * @return \Magento\Eav\Model\Entity\Attribute\Set|null
     */
    protected function getAttributeSetByName($attributeSetName)
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
        $attributeSet = $objectManager->create('Magento\Eav\Model\Entity\Attribute\Set')
            ->load($attributeSetName, 'attribute_set_name');
        if ($attributeSet->getId() === null) {
            return null;
        }
        return $attributeSet;
    }
}
