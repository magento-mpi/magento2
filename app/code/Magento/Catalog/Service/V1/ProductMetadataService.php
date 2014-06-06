<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata;

/**
 * Class ProductMetadataService
 * @package Magento\Catalog\Service\V1
 */
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
     * @var Data\Eav\OptionBuilder
     */
    private $optionBuilder;

    /**
     * @var Data\Eav\ValidationRuleBuilder
     */
    private $validationRuleBuilder;

    /**
     * @var Data\Eav\AttributeMetadataBuilder
     */
    private $attributeMetadataBuilder;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\ScopeResolverInterface $scopeResolver
     * @param Data\Eav\OptionBuilder $optionBuilder
     * @param Data\Eav\ValidationRuleBuilder $validationRuleBuilder
     * @param Data\Eav\AttributeMetadataBuilder $attributeMetadataBuilder
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ScopeResolverInterface $scopeResolver,
        Data\Eav\OptionBuilder $optionBuilder,
        Data\Eav\ValidationRuleBuilder $validationRuleBuilder,
        Data\Eav\AttributeMetadataBuilder $attributeMetadataBuilder
    ) {
        $this->eavConfig = $eavConfig;
        $this->scopeResolver = $scopeResolver;
        $this->optionBuilder = $optionBuilder;
        $this->validationRuleBuilder = $validationRuleBuilder;
        $this->attributeMetadataBuilder = $attributeMetadataBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttributesMetadata()
    {
        $customAttributes = [];
        foreach ($this->getProductAttributesMetadata() as $attributeMetadata) {
            $customAttributes[] = $attributeMetadata;
        }
        return $customAttributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductAttributesMetadata()
    {
        return $this->getAllAttributeSetMetadata(self::ENTITY_TYPE_PRODUCT, self::ATTRIBUTE_SET_ID_PRODUCT);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllAttributeSetMetadata($entityType, $attributeSetId = 0, $scopeCode = null)
    {
        if (null === $scopeCode) {
            $scopeCode = $this->scopeResolver->getScope()->getCode();
        }
        $object = new \Magento\Framework\Object(
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
            throw (new NoSuchEntityException('entityType', array($entityType)))
                ->singleField('attributeCode', $attributeCode);
        }
    }

    /**
     * @param  AbstractAttribute $attribute
     * @return Data\Eav\AttributeMetadata
     */
    private function createMetadataAttribute($attribute)
    {
        $data = $attribute->getData();

        // fill options and validate rules
        $data[AttributeMetadata::OPTIONS] = $attribute->usesSource()
            ? $attribute->getSource()->getAllOptions() : array();
        $data[AttributeMetadata::VALIDATION_RULES] = $attribute->getValidateRules();

        // fill scope
        $data[AttributeMetadata::SCOPE] = $attribute->isScopeGlobal()
            ? 'global' : ($attribute->isScopeWebsite() ? 'website' : 'store');

        // fill frontend labels
        $data[AttributeMetadata::FRONTEND_LABEL] = array(
            array(
                'store_id' => 0,
                'label' => $attribute->getFrontendLabel()
            )
        );
        if (is_array($attribute->getStoreLabels())) {
            foreach ($attribute->getStoreLabels() as $storeId => $label) {
                $data[AttributeMetadata::FRONTEND_LABEL][] = array(
                    'store_id' => $storeId,
                    'label' => $label
                );
            }
        }
        return $this->attributeMetadataBuilder->populateWithArray($data)->create();
    }
}
