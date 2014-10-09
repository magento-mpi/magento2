<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Data\Eav;

interface AttributeMetadataInterface extends \Magento\Framework\Service\Data\MetadataObjectInterface
{
    /**
     * Retrieve attribute id
     *
     * @return int
     */
    public function getId();

    /**
     * Retrieve id of the attribute.
     *
     * @return string|null
     */
    public function getAttributeId();

    /**
     * Retrieve is system attribute flag
     *
     * @return bool|null
     */
    public function isSystem();

    /**
     * Frontend HTML for input element.
     *
     * @return string|null
     */
    public function getFrontendInput();
    /**
     * Retrieve validation rules.
     *
     * @return \Magento\Catalog\Api\Data\Eav\AttributeValidationRuleInterface[]|null
     */
    public function getValidationRules();

    /**
     * Whether attribute is visible on frontend.
     *
     * @return bool|null
     */
    public function isVisible();
    /**
     * Whether attribute is required.
     *
     * @return bool|null
     */
    public function isRequired();
    /**
     * Return options of the attribute (key => value pairs for select)
     *
     * @return \Magento\Catalog\Api\Data\Eav\AttributeOptionInterface[]|null
     */
    public function getOptions();

    /**
     * Whether current attribute has been defined by a user.
     *
     * @return bool|null
     */
    public function isUserDefined();
    /**
     * Get label which supposed to be displayed on frontend.
     *
     * @return \Magento\Catalog\Api\Data\Eav\AttributeFrontendLabelInterface[]|null
     */
    public function getFrontendLabel();

    /**
     * Get the note attribute for the element.
     *
     * @return string|null
     */
    public function getNote();

    /**
     * Get backend type.
     *
     * @return string|null
     */
    public function getBackendType();
    /**
     * Get backend model
     *
     * @return string|null
     */
    public function getBackendModel();

    /**
     * Get source model
     *
     * @return string|null
     */
    public function getSourceModel();
    /**
     * Get default value for the element.
     *
     * @return string|null
     */
    public function getDefaultValue();
    /**
     * Whether this is a unique attribute
     *
     * @return string|null
     */
    public function isUnique();

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
     *  - 'downloadable'
     *
     * @return string[]|null
     */
    public function getApplyTo();

    /**
     * Whether the attribute can be used for configurable products
     *
     * @return string|null
     */
    public function isConfigurable();

    /**
     * Whether the attribute can be used in Quick Search
     *
     * @return string|null
     */
    public function isSearchable();

    /**
     * Whether the attribute can be used in Advanced Search
     *
     * @return string|null
     */
    public function isVisibleInAdvancedSearch();

    /**
     * Whether the attribute can be compared on the frontend
     *
     * @return string|null
     */
    public function isComparable();

    /**
     * Whether the attribute can be used for promo rules
     *
     * @return string|null
     */
    public function isUsedForPromoRules();

    /**
     * Whether the attribute is visible on the frontend
     *
     * @return string|null
     */
    public function isVisibleOnFront();

    /**
     * Whether the attribute can be used in product listing
     *
     * @return string|null
     */
    public function getUsedInProductListing();

    /**
     * Retrieve attribute scope
     *
     * @return string|null
     */
    public function getScope();

    /**
     * Retrieve frontend class of attribute
     *
     * @return string|null
     */
    public function getFrontendClass();

    /**
     * Enable WYSIWYG flag
     *
     * @return bool|null
     */
    public function isWysiwygEnabled();

    /**
     * Whether the HTML tags are allowed on the frontend
     *
     * @return bool|null
     */
    public function isHtmlAllowedOnFront();

    /**
     * Whether it is used for sorting in product listing
     *
     * @return bool|null
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getUsedForSortBy();

    /**
     * Whether it used in layered navigation
     *
     * @return bool|null
     */
    public function isFilterable();

    /**
     * Whether it is used in search results layered navigation
     *
     * @return bool|null
     */
    public function isFilterableInSearch();

    /**
     * Get position
     *
     * @return int|null
     */
    public function getPosition();
}
