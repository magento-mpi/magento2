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

class AttributeSetManagementTest extends WebapiAbstract
{
    public function testCreate()
    {
        $entityTypeCode = 'catalog_product';
        $entityType = $this->getEntityTypeByCode($entityTypeCode);
        $attributeSetName = 'new_attribute_set';

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/attribute-sets',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ),
            'soap' => array(
                // @todo fix SOAP configuration after SOAP tests are functional
                'operation' => '',
            ),
        );
        $arguments = array(
            'entityTypeCode' => $entityTypeCode,
            'attributeSet' => array(
                'name' => $attributeSetName,
                'sort_order' => 500,
            ),
            'skeletonId' => $entityType->getDefaultAttributeSetId(),
        );
        $result = $this->_webApiCall($serviceInfo, $arguments);
        $this->assertNotNull($result);
        $attributeSet = $this->getAttributeSetByName($attributeSetName);
        $this->assertNotNull($attributeSet);
        $this->assertEquals($attributeSet->getId(), $result['id']);
        $this->assertEquals($attributeSet->getName(), $result['name']);
        $this->assertEquals($attributeSet->getEntityTypeId(), $result['entity_type_id']);
        $this->assertEquals($attributeSet->getEntityTypeId(), $entityType->getId());
        $this->assertEquals($attributeSet->getSortOrder(), $result['sort_order']);
        $this->assertEquals($attributeSet->getSortOrder(), 500);

        // Clean up database
        $attributeSet->delete();

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

    /**
     * Retrieve entity type based on given code.
     *
     * @param string $entityTypeCode
     * @return \Magento\Eav\Model\Entity\Type|null
     */
    protected function getEntityTypeByCode($entityTypeCode)
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Eav\Model\Entity\Type $entityType */
        $entityType = $objectManager->create('Magento\Eav\Model\Config')
            ->getEntityType($entityTypeCode);
        return $entityType;
    }
}
