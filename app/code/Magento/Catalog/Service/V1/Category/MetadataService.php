<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Service\V1\Data\Eav\Category\AttributeMetadata;
use Magento\Catalog\Service\V1\Data\Eav\Category\AttributeMetadataBuilder;

class MetadataService implements MetadataServiceInterface
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
     * @var AttributeMetadataBuilder
     */
    private $attributeMetadataBuilder;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\ScopeResolverInterface $scopeResolver
     * @param AttributeMetadataBuilder $attributeMetadataBuilder
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ScopeResolverInterface $scopeResolver,
        AttributeMetadataBuilder $attributeMetadataBuilder
    ) {
        $this->eavConfig = $eavConfig;
        $this->scopeResolver = $scopeResolver;
        $this->attributeMetadataBuilder = $attributeMetadataBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttributesMetadata()
    {
        $customAttributes = [];
        foreach ($this->getCategoryAttributesMetadata() as $attributeMetadata) {
            $customAttributes[] = $attributeMetadata;
        }
        return $customAttributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryAttributesMetadata()
    {
        return $this->getAllAttributeSetMetadata(
            \Magento\Catalog\Model\Category::ENTITY,
            self::ATTRIBUTE_SET_ID_PRODUCT
        );
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
     * @return AttributeMetadata
     */
    private function createMetadataAttribute($attribute)
    {
        $data = $this->booleanPrefixMapper($attribute->getData());
        return $this->attributeMetadataBuilder->populateWithArray($data)->create();
    }

    /**
     * Remove 'is_' prefixes for Attribute fields to make DTO interface more natural
     *
     * @param  array $attributeFields
     * @return array
     */
    private function booleanPrefixMapper(array $attributeFields)
    {
        $prefix = 'is_';
        foreach ($attributeFields as $key => $value) {
            if (strpos($key, $prefix) !== 0) {
                continue;
            }
            $postfix = substr($key, strlen($prefix));
            if (!isset($attributeFields[$postfix])) {
                $attributeFields[$postfix] = $value;
                unset($attributeFields[$key]);
            }
        }
        return $attributeFields;
    }
}
