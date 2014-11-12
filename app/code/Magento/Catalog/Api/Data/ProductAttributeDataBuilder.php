<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data;
use Magento\Framework\Api\CompositeExtensibleDataBuilder;

/**
 * DataBuilder class for \Magento\Catalog\Api\Data\ProductAttributeInterface
 */
class ProductAttributeDataBuilder extends \Magento\Framework\Api\CompositeExtensibleDataBuilder
{
    /**
     * @param bool|null $isWysiwygEnabled
     * @return $this
     */
    public function setIsWysiwygEnabled($isWysiwygEnabled)
    {
        $this->set('is_wysiwyg_enabled', $isWysiwygEnabled);
        return $this;
    }

    /**
     * @param bool|null $isHtmlAllowedOnFront
     * @return $this
     */
    public function setIsHtmlAllowedOnFront($isHtmlAllowedOnFront)
    {
        $this->set('is_html_allowed_on_front', $isHtmlAllowedOnFront);
        return $this;
    }

    /**
     * @param bool|null $usedForSortBy
     * @return $this
     */
    public function setUsedForSortBy($usedForSortBy)
    {
        $this->set('used_for_sort_by', $usedForSortBy);
        return $this;
    }

    /**
     * @param bool|null $isFilterable
     * @return $this
     */
    public function setIsFilterable($isFilterable)
    {
        $this->set('is_filterable', $isFilterable);
        return $this;
    }

    /**
     * @param bool|null $isFilterableInSearch
     * @return $this
     */
    public function setIsFilterableInSearch($isFilterableInSearch)
    {
        $this->set('is_filterable_in_search', $isFilterableInSearch);
        return $this;
    }

    /**
     * @param int|null $position
     * @return $this
     */
    public function setPosition($position)
    {
        $this->set('position', $position);
        return $this;
    }

    /**
     * @param string $applyTo
     * @return $this
     */
    public function setApplyTo($applyTo)
    {
        $this->set('apply_to', $applyTo);
        return $this;
    }

    /**
     * @param string|null $isConfigurable
     * @return $this
     */
    public function setIsConfigurable($isConfigurable)
    {
        $this->set('is_configurable', $isConfigurable);
        return $this;
    }

    /**
     * @param string|null $isSearchable
     * @return $this
     */
    public function setIsSearchable($isSearchable)
    {
        $this->set('is_searchable', $isSearchable);
        return $this;
    }

    /**
     * @param string|null $isVisibleInAdvancedSearch
     * @return $this
     */
    public function setIsVisibleInAdvancedSearch($isVisibleInAdvancedSearch)
    {
        $this->set('is_visible_in_advanced_search', $isVisibleInAdvancedSearch);
        return $this;
    }

    /**
     * @param string|null $isComparable
     * @return $this
     */
    public function setIsComparable($isComparable)
    {
        $this->set('is_comparable', $isComparable);
        return $this;
    }

    /**
     * @param string|null $isUsedForPromoRules
     * @return $this
     */
    public function setIsUsedForPromoRules($isUsedForPromoRules)
    {
        $this->set('is_used_for_promo_rules', $isUsedForPromoRules);
        return $this;
    }

    /**
     * @param string|null $isVisibleOnFront
     * @return $this
     */
    public function setIsVisibleOnFront($isVisibleOnFront)
    {
        $this->set('is_visible_on_front', $isVisibleOnFront);
        return $this;
    }

    /**
     * @param string|null $usedInProductListing
     * @return $this
     */
    public function setUsedInProductListing($usedInProductListing)
    {
        $this->set('used_in_product_listing', $usedInProductListing);
        return $this;
    }

    /**
     * @param bool|null $isVisible
     * @return $this
     */
    public function setIsVisible($isVisible)
    {
        $this->set('is_visible', $isVisible);
        return $this;
    }

    /**
     * @param string|null $scope
     * @return $this
     */
    public function setScope($scope)
    {
        $this->set('scope', $scope);
        return $this;
    }

    /**
     * @param string|null $attributeId
     * @return $this
     */
    public function setAttributeId($attributeId)
    {
        $this->set('attribute_id', $attributeId);
        return $this;
    }

    /**
     * @param string|null $attributeCode
     * @return $this
     */
    public function setAttributeCode($attributeCode)
    {
        $this->set('attribute_code', $attributeCode);
        return $this;
    }

    /**
     * @param string|null $frontendInput
     * @return $this
     */
    public function setFrontendInput($frontendInput)
    {
        $this->set('frontend_input', $frontendInput);
        return $this;
    }

    /**
     * @param string|null $entityTypeId
     * @return $this
     */
    public function setEntityTypeId($entityTypeId)
    {
        $this->set('entity_type_id', $entityTypeId);
        return $this;
    }

    /**
     * @param bool|null $isRequired
     * @return $this
     */
    public function setIsRequired($isRequired)
    {
        $this->set('is_required', $isRequired);
        return $this;
    }

    /**
     * @param \Magento\Eav\Api\Data\AttributeOptionInterface $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->set('options', $options);
        return $this;
    }

    /**
     * @param bool|null $isUserDefined
     * @return $this
     */
    public function setIsUserDefined($isUserDefined)
    {
        $this->set('is_user_defined', $isUserDefined);
        return $this;
    }

    /**
     * @param mixed|null $frontendLabel
     * @return $this
     */
    public function setFrontendLabel($frontendLabel)
    {
        $this->set('frontend_label', $frontendLabel);
        return $this;
    }

    /**
     * @param \Magento\Eav\Api\Data\AttributeFrontendLabelInterface
     * $storeFrontendLabels
     * @return $this
     */
    public function setStoreFrontendLabels($storeFrontendLabels)
    {
        $this->set('store_frontend_labels', $storeFrontendLabels);
        return $this;
    }

    /**
     * @param string|null $note
     * @return $this
     */
    public function setNote($note)
    {
        $this->set('note', $note);
        return $this;
    }

    /**
     * @param string|null $backendType
     * @return $this
     */
    public function setBackendType($backendType)
    {
        $this->set('backend_type', $backendType);
        return $this;
    }

    /**
     * @param string|null $backendModel
     * @return $this
     */
    public function setBackendModel($backendModel)
    {
        $this->set('backend_model', $backendModel);
        return $this;
    }

    /**
     * @param string|null $sourceModel
     * @return $this
     */
    public function setSourceModel($sourceModel)
    {
        $this->set('source_model', $sourceModel);
        return $this;
    }

    /**
     * @param string|null $defaultValue
     * @return $this
     */
    public function setDefaultValue($defaultValue)
    {
        $this->set('default_value', $defaultValue);
        return $this;
    }

    /**
     * @param string|null $isUnique
     * @return $this
     */
    public function setIsUnique($isUnique)
    {
        $this->set('is_unique', $isUnique);
        return $this;
    }

    /**
     * @param string|null $frontendClass
     * @return $this
     */
    public function setFrontendClass($frontendClass)
    {
        $this->set('frontend_class', $frontendClass);
        return $this;
    }

    /**
     * @param \Magento\Eav\Api\Data\AttributeValidationRuleInterface $validationRules
     * @return $this
     */
    public function setValidationRules($validationRules)
    {
        $this->set('validation_rules', $validationRules);
        return $this;
    }

    /**
     * Initialize the builder
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param \Magento\Framework\Api\MetadataServiceInterface $metadataService
     * @param \Magento\Framework\ObjectManager\Config $objectManagerConfig
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager, \Magento\Framework\Api\MetadataServiceInterface $metadataService, \Magento\Framework\ObjectManager\Config $objectManagerConfig)
    {
        parent::__construct($objectManager, $metadataService, $objectManagerConfig, 'Magento\Catalog\Api\Data\ProductAttributeInterface');
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        /** TODO: temporary fix while problem with hasDataChanges flag not solved. MAGETWO-30324 */
        $object = parent::create();
        $object->setDataChanges(true);
        return $object;
    }
}
