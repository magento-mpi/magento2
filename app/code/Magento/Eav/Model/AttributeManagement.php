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
     * @var \Magento\Eav\Api\Data\AttributeInterfaceBuilder
     */
    protected $attributeBuilder;

    /**
     * @var \Magento\Eav\Model\ConfigFactory
     */
    protected $entityTypeFactory;

    /**
     * @var Attribute\GroupRepository
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
     * @var Entity\Attribute\IdentifierFactory
     */
    protected $attributeIdentifierFactory;

    /**
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $setRepository
     * @param \Magento\Eav\Api\Data\AttributeInterfaceBuilder $attributeBuilder
     * @param Resource\Entity\Attribute\Collection $attributeCollection
     * @param Config $eavConfig
     * @param ConfigFactory $entityTypeFactory
     * @param Attribute\GroupRepository $groupRepository
     * @param AttributeRepository $attributeRepository
     * @param Resource\Entity\Attribute $attributeResource
     * @param Entity\Attribute\IdentifierFactory $attributeIdentifierFactory
     */
    public function __construct(
        \Magento\Eav\Api\AttributeSetRepositoryInterface $setRepository,
        \Magento\Eav\Api\Data\AttributeInterfaceBuilder $attributeBuilder,
        \Magento\Eav\Model\Resource\Entity\Attribute\Collection $attributeCollection,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\ConfigFactory $entityTypeFactory,
        \Magento\Eav\Model\Attribute\GroupRepository $groupRepository,
        \Magento\Eav\Model\AttributeRepository $attributeRepository,
        \Magento\Eav\Model\Resource\Entity\Attribute $attributeResource,
        \Magento\Eav\Model\Entity\Attribute\IdentifierFactory $attributeIdentifierFactory
    ) {
        $this->setRepository = $setRepository;
        $this->attributeBuilder = $attributeBuilder;
        $this->attributeCollection = $attributeCollection;
        $this->eavConfig = $eavConfig;
        $this->entityTypeFactory = $entityTypeFactory;
        $this->groupRepository = $groupRepository;
        $this->attributeRepository = $attributeRepository;
        $this->attributeResource = $attributeResource;
        $this->attributeIdentifierFactory = $attributeIdentifierFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function assign($entityTypeCode, $attributeSetId, $attributeGroupId, $attributeCode, $sortOrder)
    {
        $attributeSet = $this->setRepository->get($attributeSetId);
        if (!$attributeSet->getId()) {
            throw new InputException('Attribute set does not exist');
        }
        $setEntityType = $this->entityTypeFactory->create()->getEntityType($attributeSet->getEntityTypeId());
        if ($setEntityType->getEntityTypeCode() != $entityTypeCode) {
            throw new InputException('Wrong attribute set id provided');
        }
        if (!$this->groupRepository->get($attributeGroupId)->getId()) {
            throw new InputException('Attribute group does not exist');
        }

        $attributeIdentifier = $this->attributeIdentifierFactory->create([
            'attributeCode' => $attributeCode,
            'entityTypeCode' => $entityTypeCode
        ]);

        $attribute = $this->attributeRepository->get($attributeIdentifier);
        if (!$attribute->getAttributeId()) {
            throw new InputException('Attribute does not exist');
        }

        $attribute->setId($attribute->getAttributeId());
        $attribute->setEntityTypeId($attributeSet->getEntityTypeId());
        $attribute->setAttributeSetId($attributeSetId);
        $attribute->setAttributeGroupId($attributeGroupId);
        $attribute->setSortOrder($sortOrder);

        $this->attributeResource->saveInSetIncluding(
            $attribute,
            $attribute->getAttributeId(),
            $attributeSetId,
            $attributeGroupId
        );
        return $attribute->getAttributeId();
    }

    /**
     * {@inheritdoc}
     */
    public function unassign($attributeSetId, $attributeCode)
    {
        $attributeSet = $this->setRepository->get($attributeSetId);
        if (!$attributeSet->getId()) {
            throw NoSuchEntityException::singleField('attributeSetId', $attributeSetId);
        }
        $setEntityType = $this->entityTypeFactory->create()->getEntityType($attributeSet->getEntityTypeId());

        $attributeIdentifier = $this->attributeIdentifierFactory->create([
            'attributeCode' => $attributeCode,
            'entityTypeCode' => $setEntityType->getEntityTypeCode()
        ]);
        $attribute = $this->attributeRepository->get($attributeIdentifier);

        if (!$attribute->getAttributeId()) {
            throw NoSuchEntityException::singleField('attributeId', $attribute->getAttributeId());
        }

        // check if attribute is in set
        $attribute->setAttributeSetId($attributeSet->getId())->loadEntityAttributeIdBySet();
        if (!$attribute->getEntityAttributeId()) {
            throw  new InputException('Requested attribute is not in requested attribute set.');
        }
        if (!$attribute->isUserDefined()) {
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
            $attributes[] = $this->attributeBuilder
                ->setAttributeId($attribute->getAttributeId())
                ->setCode($attribute->getAttributeCode())
                ->setFrontendLabel($attribute->getData('frontend_label'))
                ->setDefaultValue($attribute->getDefaultValue())
                ->setIsRequired((boolean)$attribute->getData('is_required'))
                ->setIsUserDefined((boolean)$attribute->getData('is_user_defined'))
                ->setFrontendInput($attribute->getData('frontend_input'))
                ->create();
        }
        return $attributes;
    }
}
