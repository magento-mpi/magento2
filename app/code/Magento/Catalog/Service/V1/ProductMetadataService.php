<?php

namespace Magento\Catalog\Service\V1;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Exception\NoSuchEntityException;


class ProductMetadataService implements ProductMetadataServiceInterface
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var \Magento\Framework\App\ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * @var \Magento\Framework\Service\Data\Eav\OptionBuilder
     */
    private $optionBuilder;

    /**
     * @var \Magento\Framework\Service\Data\Eav\ValidationRuleBuilder
     */
    private $validationRuleBuilder;

    /**
     * @var \Magento\Framework\Service\Data\Eav\AttributeMetadataBuilder
     */
    private $attributeMetadataBuilder;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\ScopeResolverInterface $scopeResolver
     * @param \Magento\Framework\Service\Data\Eav\OptionBuilder $optionBuilder
     * @param \Magento\Framework\Service\Data\Eav\ValidationRuleBuilder $validationRuleBuilder
     * @param \Magento\Framework\Service\Data\Eav\AttributeMetadataBuilder $attributeMetadataBuilder
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ScopeResolverInterface $scopeResolver
//        \Magento\Framework\Service\Data\Eav\OptionBuilder $optionBuilder,
//        \Magento\Framework\Service\Data\Eav\ValidationRuleBuilder $validationRuleBuilder,
        //\Magento\Framework\Service\Data\Eav\AttributeMetadataBuilder $attributeMetadataBuilder
    ) {
        $this->eavConfig = $eavConfig;
        $this->scopeResolver = $scopeResolver;
//        $this->optionBuilder = $optionBuilder;
//        $this->validationRuleBuilder = $validationRuleBuilder;
//        $this->attributeMetadataBuilder = $attributeMetadataBuilder;
    }

    /**
     * @inheritdoc
     */
    public function getCustomAttributeMetadata()
    {
        $customAttributes = [];
        foreach ($this->getAllProductAttributeMetadata() as $attributeMetadata) {
            if (!$attributeMetadata->isSystem()) {
                $customAttributes[] = $attributeMetadata;
            }
        }
        return $customAttributes;
    }

    /**
     * Get list of all attributes
     *
     * @param $attributeSetId
     * @return array
     */
    public function getAllAttributeMetadata($attributeSetId)
    {
        return $this->getAllAttributeSetMetadata('catalog_product', $attributeSetId);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllAttributeSetMetadata($entityType, $attributeSetId = 0, $scopeCode = null)
    {
        if (null === $scopeCode) {
            $scopeCode = $this->scopeResolver->getScope()->getCode();
        }
        $object = new \Magento\Object(
            [
                'store_id' => $scopeCode,
                'attribute_set_id' => $attributeSetId,
            ]
        );
        $attributeCodes = $this->eavConfig->getEntityAttributeCodes($entityType, $object);

        $attributesMetadata = [];
        foreach ($attributeCodes as $attributeCode) {
            try {
                $attributesMetadata[] = $this->getAttributeMetadata($entityType, $attributeCode);
            } catch (NoSuchEntityException $e) {
                //If no such entity, skip
            }
        }
        return $attributesMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeMetadata($entityType, $attributeCode)
    {
        /** @var AbstractAttribute $attribute */
        $attribute = $this->eavConfig->getAttribute($entityType, $attributeCode);
        if ($attribute) {
            $attributeMetadata = $this->createMetadataAttribute($attribute);
            return $attributeMetadata;
        } else {
            throw (new NoSuchEntityException('entityType', $entityType))
                ->addField('attributeCode', $attributeCode);
        }
    }

    /**
     * @param  AbstractAttribute
     * @return \Magento\Framework\Service\Data\Eav\AttributeMetadata
     */
    private function createMetadataAttribute($attribute)
    {
        $options = [];
        if ($attribute->usesSource()) {
            foreach ($attribute->getSource()->getAllOptions() as $option) {
                $options[] = $this->optionBuilder->setLabel($option['label'])
                    ->setValue($option['value'])
                    ->create();
            }
        }
        $validationRules = [];
        if ($attribute->getValidateRules()) {
            foreach ($attribute->getValidateRules() as $name => $value) {
                $validationRules[$name] = $this->validationRuleBuilder->setName($name)
                    ->setValue($value)
                    ->create();
            }
        }

        $this->attributeMetadataBuilder->setAttributeCode($attribute->getAttributeCode())
            ->setFrontendInput($attribute->getFrontendInput())
            ->setInputFilter($attribute->getInputFilter())
            ->setStoreLabel($attribute->getStoreLabel())
            ->setValidationRules($validationRules)
            ->setVisible($attribute->getIsVisible())
            ->setRequired($attribute->getIsRequired())
            ->setMultilineCount($attribute->getMultilineCount())
            ->setDataModel($attribute->getDataModel())
            ->setOptions($options)
            ->setFrontendClass($attribute->getFrontend()->getClass())
            ->setFrontendLabel($attribute->getFrontendLabel())
            ->setNote($attribute->getNote())
            ->setIsSystem($attribute->getIsSystem())
            ->setIsUserDefined($attribute->getIsUserDefined())
            ->setSortOrder($attribute->getSortOrder());

        return $this->attributeMetadataBuilder->create();
    }
}
