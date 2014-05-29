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

    const VALIDATION_RULES = 'validation_rules';

    const OPTIONS = 'options';

    const IS_SYSTEM = 'is_system';

    const IS_VISIBLE = 'is_visible';

    const IS_REQUIRED = 'is_required';

    const IS_USER_DEFINED = 'is_user_defined';

    const FRONTEND_LABEL = 'frontend_label';

    const NOTE = 'note';

    const BACKEND_TYPE = 'backend_type';

    const DEFAULT_VALUE = 'default_value';

    const IS_UNIQUE = 'is_unique';

    const APPLY_TO = 'apply_to';

    const IS_CONFIGURABLE = 'is_configurable';

    const IS_SEARCHABLE = 'is_searchable';

    const IS_VISIBLE_IN_ADVANCED_SEARCH = 'is_visible_in_advanced_search';

    const IS_COMPARABLE = 'is_comparable';

    const IS_USED_FOR_PROMO_RULES = 'is_used_for_promo_rules';

    const IS_VISIBLE_ON_FRONT = 'is_visible_on_front';

    const USED_IN_PRODUCT_LISTING = 'used_in_product_listing';

    const SCOPE = 'scope';

    // additional fields
    const IS_WYSIWYG_ENABLED = 'is_wysiwyg_enabled';

    const IS_HTML_ALLOWED_ON_FRONT = 'is_html_allowed_on_front';

    const FRONTEND_CLASS = 'frontend_class';

    const USED_FOR_SORT_BY = 'used_for_sort_by';

    const IS_FILTERABLE = 'is_filterable';

    const IS_FILTERABLE_IN_SEARCH = 'is_filterable_in_search';

    const POSITION = 'position';
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
     * Retrieve is system attribute flag
     *
     * @return bool
     */
    public function getIsSystem()
    {
        return $this->_get(self::IS_SYSTEM);
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
        return $this->_get(self::IS_VISIBLE);
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
     * @return string[]
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

    /**
     * Retrieve frontend class of attribute
     *
     * @return string
     */
    public function getFrontendClass()
    {
        return $this->_get(self::FRONTEND_CLASS);
    }

    /**
     * Enable WYSIWYG flag
     *
     * @return bool
     */
    public function getIsWysiwygEnabled()
    {
        return (bool)$this->_get(self::IS_WYSIWYG_ENABLED);
    }

    /**
     * Whether the HTML tags are allowed on the frontend
     *
     * @return bool
     */
    public function getIsHtmlAllowedOnFront()
    {
        return (bool)$this->_get(self::IS_HTML_ALLOWED_ON_FRONT);
    }

    /**
     * Whether it is used for sorting in product listing
     *
     * @return bool
     */
    public function getUsedForSortBy()
    {
        return (bool)$this->_get(self::USED_FOR_SORT_BY);
    }

    /**
     * Whether it used in layered navigation
     *
     * @return bool
     */
    public function getIsFilterable()
    {
        return (bool)$this->_get(self::IS_FILTERABLE);
    }

    /**
     * Whether it is used in search results layered navigation
     *
     * @return bool
     */
    public function getIsFilterableInSearch()
    {
        return (bool)$this->_get(self::IS_FILTERABLE_IN_SEARCH);
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return (int)$this->_get(self::POSITION);
    }
}
