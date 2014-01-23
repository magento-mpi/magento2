<?php
/**
 * EAV attribute metadata service
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Exception\NoSuchEntityException;

class CustomerMetadataService implements CustomerMetadataServiceInterface
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    private $_eavConfig;

    /** @var array Cache of DTOs - entityType => attributeCode => DTO */
    private $_cache;

    /**
     * @var \Magento\Customer\Model\Resource\Form\Attribute\Collection
     */
    private $_attrFormCollection;

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    private $_storeManager;

    /**
     * @var Dto\Eav\OptionBuilder
     */
    private $_optionBuilder;

    /**
     * @var Dto\Eav\AttributeMetadataBuilder
     */
    private $_attributeMetadataBuilder;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Customer\Model\Resource\Form\Attribute\Collection $attrFormCollection
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param Dto\Eav\OptionBuilder $optionBuilder
     * @param Dto\Eav\AttributeMetadataBuilder $attributeMetadataBuilder
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\Resource\Form\Attribute\Collection $attrFormCollection,
        \Magento\Core\Model\StoreManager $storeManager,
        Dto\Eav\OptionBuilder $optionBuilder,
        Dto\Eav\AttributeMetadataBuilder $attributeMetadataBuilder
    ) {
        $this->_eavConfig = $eavConfig;
        $this->_cache = [];
        $this->_attrFormCollection = $attrFormCollection;
        $this->_storeManager = $storeManager;
        $this->_optionBuilder = $optionBuilder;
        $this->_attributeMetadataBuilder = $attributeMetadataBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeMetadata($entityType, $attributeCode)
    {
        $dtoCache = $this->_getEntityCache($entityType);
        if (isset($dtoCache[$attributeCode])) {
            return $dtoCache[$attributeCode];
        }

        /** @var AbstractAttribute $attribute */
        $attribute = $this->_eavConfig->getAttribute($entityType, $attributeCode);
        if ($attribute) {
            $attributeMetadata = $this->_createMetadataAttribute($attribute);
            $dtoCache[$attributeCode] = $attributeMetadata;
            return $attributeMetadata;
        } else {
            throw (new NoSuchEntityException('entityType', $entityType))
                ->addField('attributeCode', $attributeCode);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAllAttributeSetMetadata($entityType, $attributeSetId = 0, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->_storeManager->getStore()->getId();
        }
        $object = new \Magento\Object([
            'store_id'          => $storeId,
            'attribute_set_id'  => $attributeSetId,
        ]);
        $attributeCodes = $this->_eavConfig->getEntityAttributeCodes($entityType, $object);

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
    public function getAttributes($entityType, $formCode)
    {
        $attributes = [];
        $this->_loadAttributesCollection($entityType, $formCode);
        foreach ($this->_attrFormCollection as $attribute) {
            $attributes[$attribute->getAttributeCode()] = $this->_createMetadataAttribute($attribute);
        }
        return $attributes;
    }

    /**
     * @inheritdoc
     */
    public function getCustomerAttributeMetadata($attributeCode)
    {
        return $this->getAttributeMetadata('customer', $attributeCode);
    }

    /**
     * @inheritdoc
     */
    public function getAllCustomerAttributeMetadata()
    {
        return $this->getAllAttributeSetMetadata('customer', self::CUSTOMER_ATTRIBUTE_SET_ID);
    }

    /**
     * @inheritdoc
     */
    public function getAddressAttributeMetadata($attributeCode)
    {
        return $this->getAttributeMetadata('customer_address', $attributeCode);
    }

    /**
     * @inheritdoc
     */
    public function getAllAddressAttributeMetadata()
    {
        return $this->getAllAttributeSetMetadata('customer_address', self::ADDRESS_ATTRIBUTE_SET_ID);
    }



    /**
     * Load collection with filters applied
     *
     * @param $entityType
     * @param $formCode
     * @return null
     */
    private function _loadAttributesCollection($entityType, $formCode)
    {
        $this->_attrFormCollection
            ->setStore($this->_storeManager->getStore())
            ->setEntityType($entityType)
            ->addFormCodeFilter($formCode)
            ->setSortOrder()
            ->load();
    }

    /**
     * @param AbstractAttribute $attribute
     * @return Dto\Eav\AttributeMetadata
     */
    private function _createMetadataAttribute($attribute)
    {
        $options = [];
        try {
            foreach ($attribute->getSource()->getAllOptions() as $option) {
                $options[$option['label']] = $this->_optionBuilder->setLabel($option['label'])
                    ->setValue($option['value'])
                    ->create();
            }
        } catch (\Exception $e) {
            // There is no source for this attribute
        }
        $this->_attributeMetadataBuilder->setAttributeCode($attribute->getAttributeCode())
            ->setFrontendInput($attribute->getFrontendInput())
            ->setInputFilter($attribute->getInputFilter())
            ->setStoreLabel($attribute->getStoreLabel())
            ->setValidationRules($attribute->getValidateRules())
            ->setVisible($attribute->getIsVisible())
            ->setRequired($attribute->getIsRequired())
            ->setMultilineCount($attribute->getMultilineCount())
            ->setDataModel($attribute->getDataModel())
            ->setOptions($options);

        return $this->_attributeMetadataBuilder->create();
    }

    /**
     * Helper for getting access to an entity types DTO cache.
     *
     * @param $entityType
     * @return \ArrayAccess
     */
    private function _getEntityCache($entityType)
    {
        if (!isset($this->_cache[$entityType])) {
            $this->_cache[$entityType] = new \ArrayObject();
        }
        return $this->_cache[$entityType];
    }
}
