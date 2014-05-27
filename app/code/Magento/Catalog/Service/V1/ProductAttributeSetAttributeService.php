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
     * @var \Magento\Eav\Model\Entity\AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    protected $setFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\GroupFactory
     */
    protected $groupFactory;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute
     */
    protected $attributeResource;

    /**
     * @param \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory
     * @param \Magento\Eav\Model\Entity\Attribute\GroupFactory $groupFactory
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory
     * @param \Magento\Eav\Model\Resource\Entity\Attribute $attributeResource
     */
    public function __construct(
        \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory,
        \Magento\Eav\Model\Entity\Attribute\GroupFactory $groupFactory,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory,
        \Magento\Eav\Model\Resource\Entity\Attribute $attributeResource
    ) {
        $this->attributeFactory = $attributeFactory;
        $this->groupFactory = $groupFactory;
        $this->setFactory = $setFactory;
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
        if (!$this->setFactory->create()->load($attributeSetId)->getId()) {
            throw new InputException('Attribute set does not exist');
        }

        if (!$this->groupFactory->create()->load($data->getAttributeGroupId())->getId()) {
            throw new InputException('Attribute group does not exist');
        }

        $attribute = $this->attributeFactory->create();
        if (!$attribute->load($data->getAttributeId())->getId()) {
            throw new InputException('Attribute does not exist');
        }

        $attribute->setId($data->getAttributeId());
        $attribute->setEntityTypeId(4);
        $attribute->setAttributeSetId($attributeSetId);
        $attribute->setAttributeGroupId($data->getAttributeGroupId());
        $attribute->setSortOrder($data->getSortOrder());

        $this->attributeResource->saveInSetIncluding($attribute);
        return $attribute->loadEntityAttributeIdBySet()->getData('entity_attribute_id');
    }
}
