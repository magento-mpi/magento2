<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

/**
 * Class AttributeMetadata
 */
class AttributeMetadata extends \Magento\Framework\Service\Data\AbstractObject
{
    /**#@+
     * Constants used as keys into $_data
     */
    const ATTRIBUTE_ID = 'attribute_id';

    const ATTRIBUTE_CODE = 'attribute_code';

    const FRONTEND_INPUT = 'frontend_input';

    const STORE_LABEL = 'store_label';

    const VALIDATION_RULES = 'validation_rules';

    const OPTIONS = 'options';

    const VISIBLE = 'visible';

    const IS_REQUIRED = 'is_required';

    const IS_USER_DEFINED = 'is_user_defined';

    const SORT_ORDER = 'sort_order';

    const FRONTEND_LABEL = 'frontend_label';

    const IS_SYSTEM = 'is_system';

    const NOTE = 'note';

    const BACKEND_TYPE = 'backend_type';

    const DEFAULT_VALUE = 'default_value';

    const IS_UNIQUE = 'is_unique';

    const APPLY_TO = 'apply_to';

    const IS_CONFIGURABLE = 'is_configurable';

    const IS_SEARCHABLE = 'is_searchable';

    const IS_FILTERABLE = 'is_filterable';

    const IS_FILTERABLE_IN_SEARCH = 'is_filterable_in_search';

    const IS_VISIBLE_IN_ADVANCED_SEARCH = 'is_visible_in_advanced_search';

    const IS_COMPARABLE = 'is_comparable';

    const IS_USED_FOR_PROMO_RULES = 'is_used_for_promo_rules';

    const IS_VISIBLE_ON_FRONT = 'is_visible_on_front';

    const USED_IN_PRODUCT_LISTING = 'used_in_product_listing';

    const SCOPE = 'scope';
    /**#@-*/

    /**
     * Retrieve id of the attribute.
     *
     * @return string
     */
    public function getAttributeId()
    {
        return $this->_get(self::ATTRIBUTE_ID);
    }

    /**
     * Retrieve code of the attribute.
     *
     * @return string
     */
    public function getAttributeCode()
    {
        return $this->_get(self::ATTRIBUTE_CODE);
    }

    /**
     * Frontend HTML for input element.
     *
     * @return string
     */
    public function getFrontendInput()
    {
        return $this->_get(self::FRONTEND_INPUT);
    }

    /**
     * Get label of the store.
     *
     * @return string
     */
    public function getStoreLabel()
    {
        return $this->_get(self::STORE_LABEL);
    }

    /**
     * Retrieve validation rules.
     *
     * @return \Magento\Catalog\Service\V1\Data\Eav\ValidationRule[]
     */
    public function getValidationRules()
    {
        return $this->_get(self::VALIDATION_RULES);
    }

    /**
     * Whether attribute is visible on frontend.
     *
     * @return bool
     */
    public function getIsVisible()
    {
        return $this->_get(self::VISIBLE);
    }

    /**
     * Whether attribute is required.
     *
     * @return bool
     */
    public function getIsRequired()
    {
        return $this->_get(self::IS_REQUIRED);
    }

    /**
     * Return options of the attribute (key => value pairs for select)
     *
     * @return \Magento\Catalog\Service\V1\Data\Eav\Option[]
     */
    public function getOptions()
    {
        return $this->_get(self::OPTIONS);
    }

    /**
     * Whether current attribute has been defined by a user.
     *
     * @return bool
     */
    public function getIsUserDefined()
    {
        return $this->_get(self::IS_USER_DEFINED);
    }

    /**
     * Get attributes sort order.
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->_get(self::SORT_ORDER);
    }

    /**
     * Get label which supposed to be displayed on frontend.
     *
     * @return string
     */
    public function getFrontendLabel()
    {
        return $this->_get(self::FRONTEND_LABEL);
    }

    /**
     * Get the note attribute for the element.
     *
     * @return string
     */
    public function getNote()
    {
        return $this->_get(self::NOTE);
    }

    /**
     * Whether this is a system attribute.
     *
     * @return bool
     */
    public function getIsSystem()
    {
        return $this->_get(self::IS_SYSTEM);
    }

    /**
     * Get backend type.
     *
     * @return string
     */
    public function getBackendType()
    {
        return $this->_get(self::BACKEND_TYPE);
    }

    /**
     * Get default value for the element.
     *
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->_get(self::DEFAULT_VALUE);
    }

    /**
     * Whether this is a unique attribute
     *
     * @return string
     */
    public function getIsUnique()
    {
        return $this->_get(self::IS_UNIQUE);
    }

    /**
     * Get apply to value for the element
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
     * @return string
     */
    public function getApplyTo()
    {
        return $this->_get(self::APPLY_TO);
    }

    /**
     * Whether the attribute can be used for configurable products
     *
     * @return string
     */
    public function getIsConfigurable()
    {
        return $this->_get(self::IS_CONFIGURABLE);
    }

    /**
     * Whether the attribute can be used in Quick Search
     *
     * @return string
     */
    public function getIsSearchable()
    {
        return $this->_get(self::IS_SEARCHABLE);
    }

    /**
     * Whether the attribute uses for filtering
     *
     * @return string
     */
    public function getIsFilterable()
    {
        return $this->_get(self::IS_FILTERABLE);
    }

    /**
     * Whether the attribute uses for filtering
     *
     * @return string
     */
    public function getIsFilterableInSearch()
    {
        return $this->_get(self::IS_FILTERABLE_IN_SEARCH);
    }

    /**
     * Whether the attribute can be used in Advanced Search
     *
     * @return string
     */
    public function getIsVisibleInAdvancedSearch()
    {
        return $this->_get(self::IS_VISIBLE_IN_ADVANCED_SEARCH);
    }

    /**
     * Whether the attribute can be compared on the frontend
     *
     * @return string
     */
    public function getIsComparable()
    {
        return $this->_get(self::IS_COMPARABLE);
    }

    /**
     * Whether the attribute can be used for promo rules
     *
     * @return string
     */
    public function getIsUsedForPromoRules()
    {
        return $this->_get(self::IS_USED_FOR_PROMO_RULES);
    }

    /**
     * Whether the attribute is visible on the frontend
     *
     * @return string
     */
    public function getIsVisibleOnFront()
    {
        return $this->_get(self::IS_VISIBLE_ON_FRONT);
    }

    /**
     * Whether the attribute can be used in product listing
     *
     * @return string
     */
    public function getUsedInProductListing()
    {
        return $this->_get(self::USED_IN_PRODUCT_LISTING);
    }

    /**
     * Retrieve attribute scope
     *
     * @return string
     */
    public function getScope()
    {
        return $this->_get(self::SCOPE);
    }
}
