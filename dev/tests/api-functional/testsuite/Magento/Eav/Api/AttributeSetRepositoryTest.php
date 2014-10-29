<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api;

use Magento\TestFramework\TestCase\WebapiAbstract,
    Magento\Webapi\Model\Rest\Config as RestConfig;

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
        $this->assertEquals($attributeSet->getId(), $result['id']);
        $this->assertEquals($attributeSet->getName(), $result['name']);
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
                'id' => $attributeSet->getId(),
                'name' => $attributeSet->getName(), // name is the same, because it is used by fixture rollback script
                'entity_type_id' => $attributeSet->getEntityTypeId(),
                'sort_order' => $updatedSortOrder,
            ),
        );
        $result = $this->_webApiCall($serviceInfo, $arguments);
        $this->assertNotNull($result);
        // Reload attribute set data
        $attributeSet = $this->getAttributeSetByName($attributeSetName);
        $this->assertEquals($attributeSet->getId(), $result['id']);
        $this->assertEquals($attributeSet->getName(), $result['name']);
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

    public function testGetList()
    {
        $this->markTestIncomplete('Implement this test when framework provides search result builders');
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
