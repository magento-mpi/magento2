<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

use Magento\Framework\Service\Data\AbstractObjectBuilder;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;

/**
 * Class AttributeMetadataBuilder
 */
class AttributeMetadataBuilder extends AbstractObjectBuilder
{
    /**
     * Option builder
     *
     * @var OptionBuilder
     */
    protected $optionBuilder;

    /**
     * Validation rule builder
     *
     * @var ValidationRuleBuilder
     */
    protected $validationRuleBuilder;

    /**
     * Initializes builder.
     *
     * @param OptionBuilder $optionBuilder
     * @param ValidationRuleBuilder $validationRuleBuilder
     * @param \Magento\Framework\Service\Data\ObjectFactory $objectFactory
     */
    public function __construct(
        \Magento\Framework\Service\Data\ObjectFactory $objectFactory,
        OptionBuilder $optionBuilder,
        ValidationRuleBuilder $validationRuleBuilder
    ) {
        parent::__construct($objectFactory);
        $this->optionBuilder = $optionBuilder;
        $this->validationRuleBuilder = $validationRuleBuilder;
        $this->_data[AttributeMetadata::OPTIONS] = array();
        $this->_data[AttributeMetadata::VALIDATION_RULES] = array();
    }

    /**
     * Set attribute id
     *
     * @param  int $attributeId
     * @return $this
     */
    public function setAttributeId($attributeId)
    {
        return $this->_set(AttributeMetadata::ATTRIBUTE_ID, $attributeId);
    }

    /**
     * Set attribute code
     *
     * @param  string $attributeCode
     * @return $this
     */
    public function setAttributeCode($attributeCode)
    {
        return $this->_set(AttributeMetadata::ATTRIBUTE_CODE, $attributeCode);
    }

    /**
     * Set attribute as system
     *
     * @param  bool $isSystem
     * @return $this
     */
    public function setIsSystem($isSystem)
    {
        return $this->_set(AttributeMetadata::IS_SYSTEM, $isSystem);
    }

    /**
     * Set front end input
     *
     * @param  string $frontendInput
     * @return $this
     */
    public function setFrontendInput($frontendInput)
    {
        return $this->_set(AttributeMetadata::FRONTEND_INPUT, $frontendInput);
    }

    /**
     * Set validation rules
     *
     * @param  \Magento\Catalog\Service\V1\Data\Eav\ValidationRule[] $validationRules
     * @return $this
     */
    public function setValidationRules($validationRules)
    {
        return $this->_set(AttributeMetadata::VALIDATION_RULES, $validationRules);
    }

    /**
     * Set options
     * 
     * @param  \Magento\Catalog\Service\V1\Data\Eav\Option[] $options
     * @return $this
     */
    public function setOptions($options)
    {
        return $this->_set(AttributeMetadata::OPTIONS, $options);
    }

    /**
     * Set visible
     *
     * @param  bool $visible
     * @return $this
     */
    public function setIsVisible($visible)
    {
        return $this->_set(AttributeMetadata::IS_VISIBLE, $visible);
    }

    /**
     * Set required
     *
     * @param  bool $required
     * @return $this
     */
    public function setIsRequired($required)
    {
        return $this->_set(AttributeMetadata::IS_REQUIRED, $required);
    }

    /**
     * Set is user defined
     *
     * @param  bool $isUserDefined
     * @return $this
     */
    public function setIsUserDefined($isUserDefined)
    {
        return $this->_set(AttributeMetadata::IS_USER_DEFINED, $isUserDefined);
    }

    /**
     * Set front end label
     *
     * @param  string $frontendLabel
     * @return $this
     */
    public function setFrontendLabel($frontendLabel)
    {
        return $this->_set(AttributeMetadata::FRONTEND_LABEL, $frontendLabel);
    }

    /**
     * Set note
     *
     * @param  string $note
     * @return $this
     */
    public function setNote($note)
    {
        return $this->_set(AttributeMetadata::NOTE, $note);
    }

    /**
     * @param  string $backendType
     * @return AttributeMetadataBuilder
     */
    public function setBackendType($backendType)
    {
        return $this->_set(AttributeMetadata::BACKEND_TYPE, $backendType);
    }

    /**
     * Set default value for the element
     *
     * @param  mixed $value
     * @return $this
     */
    public function setDefaultValue($value)
    {
        return $this->_set(AttributeMetadata::DEFAULT_VALUE, $value);
    }

    /**
     * Set whether this is a unique attribute
     *
     * @param  bool $isUnique
     * @return $this
     */
    public function setIsUnique($isUnique)
    {
        return $this->_set(AttributeMetadata::IS_UNIQUE, $isUnique);
    }

    /**
     * Set apply to value for the element
     *
     * Apply to. Empty for "Apply to all"
     * or array of the following possible values:
     *  - 'simple',
     *  - 'grouped',
     *  - 'configurable',
     *  - 'virtual',
     *  - 'bundle',
     *  - 'downloadable',
     *  - 'giftcard'
     *
     * @param  array|string|null $applyTo
     * @return $this
     */
    public function setApplyTo($applyTo)
    {
        return $this->_set(AttributeMetadata::APPLY_TO, $this->processApplyToValue($applyTo));
    }

    /**
     * Process applyTo value
     *
     * Transform string to array
     *
     * @param  string|array $applyTo
     * @return array
     */
    protected function processApplyToValue($applyTo)
    {
        $value = array();
        if (is_array($applyTo)) {
            $value = $applyTo;
        } elseif (is_string($applyTo)) {
            $value = explode(',', $applyTo);
        }
        return $value;
    }

    /**
     * Set whether the attribute can be used for configurable products
     *
     * @param  bool $isConfigurable
     * @return $this
     */
    public function setIsConfigurable($isConfigurable)
    {
        return $this->_set(AttributeMetadata::IS_CONFIGURABLE, $isConfigurable);
    }

    /**
     * Set whether the attribute can be used in Quick Search
     *
     * @param  bool $isSearchable
     * @return $this
     */
    public function setIsSearchable($isSearchable)
    {
        return $this->_set(AttributeMetadata::IS_SEARCHABLE, $isSearchable);
    }

    /**
     * Set whether the attribute can be used in Advanced Search
     *
     * @param  bool $isVisibleInAdvancedSearch
     * @return $this
     */
    public function setIsVisibleInAdvancedSearch($isVisibleInAdvancedSearch)
    {
        return $this->_set(AttributeMetadata::IS_VISIBLE_IN_ADVANCED_SEARCH, $isVisibleInAdvancedSearch);
    }

    /**
     * Set whether the attribute can be compared on the frontend
     *
     * @param  bool $isComparable
     * @return $this
     */
    public function setIsComparable($isComparable)
    {
        return $this->_set(AttributeMetadata::IS_COMPARABLE, $isComparable);
    }

    /**
     * Set whether the attribute can be used for promo rules
     *
     * @param  bool $isUsedForPromoRules
     * @return $this
     */
    public function setIsUsedForPromoRules($isUsedForPromoRules)
    {
        return $this->_set(AttributeMetadata::IS_USED_FOR_PROMO_RULES, $isUsedForPromoRules);
    }

    /**
     * Set whether the attribute is visible on the frontend
     *
     * @param  bool $isVisibleOnFront
     * @return $this
     */
    public function setIsVisibleOnFront($isVisibleOnFront)
    {
        return $this->_set(AttributeMetadata::IS_VISIBLE_ON_FRONT, $isVisibleOnFront);
    }

    /**
     * Set whether the attribute can be used in product listing
     *
     * @param  bool $usedInProductListing
     * @return $this
     */
    public function setUsedInProductListing($usedInProductListing)
    {
        return $this->_set(AttributeMetadata::USED_IN_PRODUCT_LISTING, $usedInProductListing);
    }

    /**
     * Set attribute scope value
     *
     * @param  string $scope
     * @return $this
     */
    public function setScope($scope)
    {
        return $this->_set(AttributeMetadata::SCOPE, $scope);
    }

    /**
     * Set whether it is used for sorting in product listing
     *
     * @param  bool $usedForSortBy
     * @return $this
     */
    public function setUsedForSortBy($usedForSortBy)
    {
        return $this->_set(AttributeMetadata::USED_FOR_SORT_BY, (bool)$usedForSortBy);
    }

    /**
     * Set whether it used in layered navigation
     *
     * @param  bool $isFilterable
     * @return $this
     */
    public function setIsFilterable($isFilterable)
    {
        return $this->_set(AttributeMetadata::IS_FILTERABLE, (bool)$isFilterable);
    }

    /**
     * Set whether it is used in search results layered navigation
     *
     * @param  bool $isFilterableInSearch
     * @return $this
     */
    public function setIsFilterableInSearch($isFilterableInSearch)
    {
        return $this->_set(AttributeMetadata::IS_FILTERABLE_IN_SEARCH, (bool)$isFilterableInSearch);
    }

    /**
     * Set position
     *
     * @param  int $position
     * @return $this
     */
    public function setPosition($position)
    {
        return $this->_set(AttributeMetadata::POSITION, (int)$position);
    }

    /**
     * Set whether WYSIWYG enabled or not
     *
     * @param  bool $isWysiwygEnabled
     * @return $this
     */
    public function setIsWysiwygEnabled($isWysiwygEnabled)
    {
        return $this->_set(AttributeMetadata::IS_WYSIWYG_ENABLED, (bool)$isWysiwygEnabled);
    }

    /**
     * Set whether the HTML tags are allowed on the frontend
     *
     * @param  bool $isHtmlAllowedOnFront
     * @return $this
     */
    public function setIsHtmlAllowedOnFront($isHtmlAllowedOnFront)
    {
        return $this->_set(AttributeMetadata::IS_HTML_ALLOWED_ON_FRONT, (bool)$isHtmlAllowedOnFront);
    }

    /**
     * Set frontend class for attribute
     *
     * @param  string $frontendClass
     * @return $this
     */
    public function setFrontendClass($frontendClass)
    {
        return $this->_set(AttributeMetadata::FRONTEND_CLASS, $frontendClass);
    }

    /**
     * {@inheritdoc}
     */
    protected function _setDataValues(array $data)
    {
        if (array_key_exists(AttributeMetadata::OPTIONS, $data)) {
            $options = array();
            if (is_array($data[AttributeMetadata::OPTIONS])) {
                foreach ($data[AttributeMetadata::OPTIONS] as $key => $option) {
                    $options[$key] = $this->optionBuilder->populateWithArray($option)->create();
                }
            }
            $validationRules = array();
            if (is_array($data[AttributeMetadata::VALIDATION_RULES])) {
                foreach ($data[AttributeMetadata::VALIDATION_RULES] as $key => $value) {
                    $validationRules[$key] = $this->validationRuleBuilder->populateWithArray($value)->create();
                }
            }

            $data[AttributeMetadata::OPTIONS] = $options;
            $data[AttributeMetadata::VALIDATION_RULES] = $validationRules;
        }

        if (array_key_exists(AttributeMetadata::APPLY_TO, $data)) {
            $data[AttributeMetadata::APPLY_TO] = $this->processApplyToValue($data[AttributeMetadata::APPLY_TO]);
        }

        return parent::_setDataValues($data);
    }
}
