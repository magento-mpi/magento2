<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;

class AttributeManagement implements \Magento\Eav\Api\AttributeManagementInterface
{
    /**
     * @var \Magento\Eav\Api\AttributeSetRepositoryInterface
     */
    protected $setRepository;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\Collection
     */
    protected $attributeCollection;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Eav\Api\Data\AttributeInterfaceDataBuilder
     */
    protected $attributeBuilder;

    /**
     * @var \Magento\Eav\Model\ConfigFactory
     */
    protected $entityTypeFactory;

    /**
     * @var \Magento\Eav\Api\AttributeGroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var AttributeRepository
     */
    protected $attributeRepository;

    /**
     * @var Resource\Entity\Attribute
     */
    protected $attributeResource;

    /**
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $setRepository
     * @param \Magento\Eav\Api\Data\AttributeInterfaceDataBuilder $attributeBuilder
     * @param Resource\Entity\Attribute\Collection $attributeCollection
     * @param Config $eavConfig
     * @param ConfigFactory $entityTypeFactory
     * @param \Magento\Eav\Api\AttributeGroupRepositoryInterface $groupRepository
     * @param AttributeRepository $attributeRepository
     * @param Resource\Entity\Attribute $attributeResource
     */
    public function __construct(
        \Magento\Eav\Api\AttributeSetRepositoryInterface $setRepository,
        \Magento\Eav\Api\Data\AttributeInterfaceDataBuilder $attributeBuilder,
        \Magento\Eav\Model\Resource\Entity\Attribute\Collection $attributeCollection,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\ConfigFactory $entityTypeFactory,
        \Magento\Eav\Api\AttributeGroupRepositoryInterface $groupRepository,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
        \Magento\Eav\Model\Resource\Entity\Attribute $attributeResource
    ) {
        $this->setRepository = $setRepository;
        $this->attributeBuilder = $attributeBuilder;
        $this->attributeCollection = $attributeCollection;
        $this->eavConfig = $eavConfig;
        $this->entityTypeFactory = $entityTypeFactory;
        $this->groupRepository = $groupRepository;
        $this->attributeRepository = $attributeRepository;
        $this->attributeResource = $attributeResource;
    }

    /**
     * {@inheritdoc}
     */
    public function assign($entityTypeCode, $attributeSetId, $attributeGroupId, $attributeCode, $sortOrder)
    {
        try {
            $attributeSet = $this->setRepository->get($attributeSetId);
        } catch (NoSuchEntityException $ex) {
            throw new NoSuchEntityException(sprintf('AttributeSet with id "%s" does not exist.', $attributeSetId));
        }

        $setEntityType = $this->entityTypeFactory->create()->getEntityType($attributeSet->getEntityTypeId());
        if ($setEntityType->getEntityTypeCode() != $entityTypeCode) {
            throw new InputException('Wrong attribute set id provided');
        }
        //Check if group exists. If not - expected exception
        $this->groupRepository->get($attributeGroupId);

        /** @var \Magento\Eav\Api\Data\AttributeInterface $attribute */
        $attribute = $this->attributeRepository->get($entityTypeCode, $attributeCode);

        $this->attributeResource->saveInSetIncluding(
            $attribute,
            $attribute->getAttributeId(),
            $attributeSetId,
            $attributeGroupId,
            $sortOrder
        );
        $attribute->setAttributeSetId($attributeSetId);
        return $attribute->loadEntityAttributeIdBySet()->getData('entity_attribute_id');
    }

    /**
     * {@inheritdoc}
     */
    public function unassign($attributeSetId, $attributeCode)
    {
        $attributeSet = $this->setRepository->get($attributeSetId);
        $setEntityType = $this->entityTypeFactory->create()->getEntityType($attributeSet->getEntityTypeId());

        /** @var \Magento\Catalog\Model\Resource\Eav\Attribute $attribute */
        $attribute = $this->attributeRepository->get($setEntityType->getEntityTypeCode(), $attributeCode);

        // Check if attribute is in set
        $attribute->setAttributeSetId($attributeSet->getId());
        $attribute->loadEntityAttributeIdBySet();

        if (!$attribute->getEntityAttributeId()) {
            throw  new InputException('Requested attribute is not in requested attribute set.');
        }
        if (!$attribute->getIsUserDefined()) {
            throw new StateException('System attribute can not be deleted');
        }
        $attribute->deleteEntity();
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($entityType, $attributeSetId)
    {
        /** @var \Magento\Eav\Api\Data\AttributeSetInterface $attributeSet */
        $attributeSet = $this->setRepository->get($attributeSetId);
        $requiredEntityTypeId = $this->eavConfig->getEntityType($entityType)->getId();
        if (!$attributeSet->getId() || $attributeSet->getEntityTypeId() != $requiredEntityTypeId) {
            throw NoSuchEntityException::singleField('attributeSetId', $attributeSetId);
        }

        $attributeCollection = $this->attributeCollection->setAttributeSetFilter($attributeSet->getId())->load();
        $attributes = [];
        /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
        foreach ($attributeCollection as $attribute) {
            $attributes[] = $this->attributeBuilder->populateWithArray($attribute->getData())->create();
        }
        return $attributes;
    }
}
