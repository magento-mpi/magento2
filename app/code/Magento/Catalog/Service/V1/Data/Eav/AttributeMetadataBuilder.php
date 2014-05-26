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
     */
    public function __construct(
        OptionBuilder $optionBuilder,
        ValidationRuleBuilder $validationRuleBuilder
    ) {
        parent::__construct();
        $this->optionBuilder = $optionBuilder;
        $this->validationRuleBuilder = $validationRuleBuilder;
        $this->_data[AttributeMetadata::OPTIONS] = array();
        $this->_data[AttributeMetadata::VALIDATION_RULES] = array();
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
     * Set store label
     *
     * @param  string $storeLabel
     * @return $this
     */
    public function setStoreLabel($storeLabel)
    {
        return $this->_set(AttributeMetadata::STORE_LABEL, $storeLabel);
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
    public function setVisible($visible)
    {
        return $this->_set(AttributeMetadata::VISIBLE, $visible);
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
     * Set sort order
     *
     * @param  int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {
        return $this->_set(AttributeMetadata::SORT_ORDER, $sortOrder);
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
     * Set is system
     *
     * @param  bool $isSystem
     * @return $this
     */
    public function setIsSystem($isSystem)
    {
        return $this->_set(AttributeMetadata::IS_SYSTEM, $isSystem);
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
     * @param  array|null $applyTo
     * @return $this
     */
    public function setApplyTo($applyTo)
    {
        return $this->_set(AttributeMetadata::APPLY_TO, $applyTo);
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

        return parent::_setDataValues($data);
    }
}
