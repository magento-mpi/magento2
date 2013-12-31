<?php
/**
 * EAV attribute metadata service
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\Eav;

use Magento\Customer\Service\Entity\V1\Eav\AttributeMetadata;
use Magento\Customer\Service\Entity\V1\Eav\Option;

class AttributeMetadataServiceV1 implements AttributeMetadataServiceV1Interface
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /** @var array Cache of DTOs - entityType => attributeCode => DTO */
    protected $_cache;

    /**
     * @var \Magento\Customer\Model\Resource\Form\Attribute\Collection
     */
    protected $_attrFormCollection;

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\Resource\Form\Attribute\Collection $attrFormCollection,
        \Magento\Core\Model\StoreManager $storeManager
    )
    {
        $this->_eavConfig = $eavConfig;
        $this->_cache = [];
        $this->_attrFormCollection = $attrFormCollection;
        $this->_storeManager = $storeManager;
    }

    /**
     * Retrieve EAV attribute metadata
     *
     * @param   mixed $entityType
     * @param   mixed $attributeCode
     * @return AttributeMetadata
     */
    public function getAttributeMetadata($entityType, $attributeCode)
    {
        $dtoCache = $this->_getEntityCache($entityType);
        if (isset($dtoCache[$attributeCode])) {
            return $dtoCache[$attributeCode];
        }

        /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute */
        $attribute = $this->_eavConfig->getAttribute($entityType, $attributeCode);
        $attributeMetadata = $this->_createMetadataAttribute($attribute);
        $dtoCache[$attributeCode] = $attributeMetadata;
        $attributeMetadata->lock();
        return $attributeMetadata;
    }

    /**
     * Returns all known attributes metadata for a given entity type and attribute set
     *
     * @param string $entityType
     * @param int $attributeSetId
     * @param int $storeId
     * @return AttributeMetadata[]
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
            $attributesMetadata[] = $this->getAttributeMetadata($entityType, $attributeCode);
        }
        return $attributesMetadata;
    }

    /**
     * Retrieve all attributes for entityType filtered by form code
     *
     * @param $entityType
     * @param $formCode
     * @return AttributeMetadata[]
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
     * Load collection with filters applied
     *
     * @param $entityType
     * @param $formCode
     * @return null
     */
    protected function _loadAttributesCollection($entityType, $formCode)
    {
         $this->_attrFormCollection
            ->setStore($this->_storeManager->getStore())
            ->setEntityType($entityType)
            ->addFormCodeFilter($formCode)
            ->setSortOrder()
            ->load();
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
     * @return AttributeMetadata
     */
    protected function _createMetadataAttribute($attribute)
    {
        $options = [];
        try {
            foreach ($attribute->getSource()->getAllOptions() as $option) {
                $options[$option['label']] = new Option(
                    $option['label'],
                    $option['value']
                );
            }
        } catch (\Exception $e) {
            // There is no source for this attribute
        }
        $attributeMetadata = new AttributeMetadata();
        $attributeMetadata->setAttributeCode($attribute->getAttributeCode())
            ->setFrontendInput($attribute->getFrontendInput())
            ->setInputFilter($attribute->getInputFilter())
            ->setStoreLabel($attribute->getStoreLabel())
            ->setValidationRules($attribute->getValidateRules())
            ->setIsVisible($attribute->getIsVisible())
            ->setIsRequired($attribute->getIsRequired())
            ->setMultilineCount($attribute->getMultilineCount())
            ->setDataModel($attribute->getDataModel())
            ->setOptions($options);

        return $attributeMetadata;
    }

    /**
     * Save EAV attribute metadata
     *
     * @param AttributeMetadata $attribute
     * @return AttributeMetadata
     */
    public function saveAttributeMetadata(AttributeMetadata $attribute)
    {
        //TODO
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
