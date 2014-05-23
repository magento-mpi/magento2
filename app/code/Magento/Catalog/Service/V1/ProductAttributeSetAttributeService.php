<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\Framework\Exception\InputException;

class ProductAttributeSetAttributeService implements ProductAttributeSetAttributeServiceInterface
{
    /**
     * @var \Magento\Eav\Model\Entity\Attribute $attribute
     */
    protected $attribute;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Set
     */
    protected $attributeSet;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Group
     */
    protected $attributeGroup;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute
     */
    protected $attributeResource;

    /**
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @param \Magento\Eav\Model\Entity\Attribute\Group $attributeGroup
     * @param \Magento\Eav\Model\Entity\Attribute\Set $attributeSet
     * @param \Magento\Eav\Model\Resource\Entity\Attribute $attributeResource
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Attribute $attribute,
        \Magento\Eav\Model\Entity\Attribute\Group $attributeGroup,
        \Magento\Eav\Model\Entity\Attribute\Set $attributeSet,
        \Magento\Eav\Model\Resource\Entity\Attribute $attributeResource
    ) {
        $this->attribute = $attribute;
        $this->attributeGroup = $attributeGroup;
        $this->attributeSet = $attributeSet;
        $this->attributeResource = $attributeResource;
    }

    /**
     * Add attribute to attribute set and group
     *
     * @param int $attributeSetId
     * @param Data\Eav\AttributeSet\Attribute $data
     * @return int
     * @throws InputException
     */
    public function addAttribute($attributeSetId, \Magento\Catalog\Service\V1\Data\Eav\AttributeSet\Attribute $data)
    {
        if (!$this->attributeSet->load($attributeSetId)->getId()) {
            throw new InputException('Attribute set does not exist');
        }

        if (!$this->attributeGroup->load($data->getAttributeGroupId())->getId()) {
            throw new InputException('Attribute group does not exist');
        }

        if (!$this->attribute->load($data->getAttributeId())->getId()) {
            throw new InputException('Attribute does not exist');
        }

        $this->attribute->setId($data->getAttributeId());
        $this->attribute->setEntityTypeId(4);
        $this->attribute->setAttributeSetId($attributeSetId);
        $this->attribute->setAttributeGroupId($data->getAttributeGroupId());
        $this->attribute->setSortOrder($data->getSortOrder());

        $this->attributeResource->saveInSetIncluding($this->attribute);
        return $this->attribute->loadEntityAttributeIdBySet()->getData('entity_attribute_id');
    }
}
