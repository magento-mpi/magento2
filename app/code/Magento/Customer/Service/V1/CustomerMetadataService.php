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

class CustomerMetadataService implements CustomerMetadataServiceInterface
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    private $_eavConfig;

    /**
     * @var \Magento\Customer\Model\Resource\Form\Attribute\CollectionFactory
     */
    private $_attrFormCollectionFactory;

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
     * @param \Magento\Customer\Model\Resource\Form\Attribute\CollectionFactory $attrFormCollectionFactory
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param Dto\Eav\OptionBuilder $optionBuilder
     * @param Dto\Eav\AttributeMetadataBuilder $attributeMetadataBuilder
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\Resource\Form\Attribute\CollectionFactory $attrFormCollectionFactory,
        \Magento\Core\Model\StoreManager $storeManager,
        Dto\Eav\OptionBuilder $optionBuilder,
        Dto\Eav\AttributeMetadataBuilder $attributeMetadataBuilder
    ) {
        $this->_eavConfig = $eavConfig;
        $this->_attrFormCollectionFactory = $attrFormCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_optionBuilder = $optionBuilder;
        $this->_attributeMetadataBuilder = $attributeMetadataBuilder;
    }

    /**
     * Retrieve EAV attribute metadata
     *
     * @param   mixed $entityType
     * @param   mixed $attributeCode
     * @return Dto\Eav\AttributeMetadata
     */
    public function getAttributeMetadata($entityType, $attributeCode)
    {
        /** @var AbstractAttribute $attribute */
        $attribute = $this->_eavConfig->getAttribute($entityType, $attributeCode);
        $attributeMetadata = $this->_createMetadataAttribute($attribute);
        return $attributeMetadata;
    }

    /**
     * Returns all known attributes metadata for a given entity type and attribute set
     *
     * @param string $entityType
     * @param int $attributeSetId
     * @param int $storeId
     * @return Dto\Eav\AttributeMetadata[]
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
     * @return Dto\Eav\AttributeMetadata[]
     */
    public function getAttributes($entityType, $formCode)
    {
        $attributes = [];
        $attributesFormCollection = $this->_loadAttributesCollection($entityType, $formCode);
        foreach ($attributesFormCollection as $attribute) {
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
     * @return \Magento\Customer\Model\Resource\Form\Attribute\Collection
     */
    private function _loadAttributesCollection($entityType, $formCode)
    {
        $attributesFormCollection = $this->_attrFormCollectionFactory->create();
        $attributesFormCollection->setStore($this->_storeManager->getStore())
            ->setEntityType($entityType)
            ->addFormCodeFilter($formCode)
            ->setSortOrder();

        return $attributesFormCollection;
    }

    /**
     * @param \Magento\Customer\Model\Attribute $attribute
     * @return Dto\Eav\AttributeMetadata
     */
    private function _createMetadataAttribute($attribute)
    {
        $options = [];
        if ($attribute->usesSource()) {
            foreach ($attribute->getSource()->getAllOptions() as $option) {
                $options[$option['label']] = $this->_optionBuilder->setLabel($option['label'])
                    ->setValue($option['value'])
                    ->create();
            }
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
            ->setOptions($options)
            ->setFrontendClass($attribute->getFrontend()->getClass())
            ->setFrontendLabel($attribute->getFrontendLabel())
            ->setIsSystem($attribute->getIsSystem())
            ->setIsUserDefined($attribute->getIsUserDefined())
            ->setSortOrder($attribute->getSortOrder());

        return $this->_attributeMetadataBuilder->create();
    }

}
