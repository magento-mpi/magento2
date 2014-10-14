<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Data;

interface EavAttributeInterface extends \Magento\Eav\Api\Data\AttributeInterface
{
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
     * Whether attribute is visible on frontend.
     *
     * @return bool|null
     */
    public function isVisible();

    /**
     * Retrieve is system attribute flag
     *
     * @return bool|null
     */
    public function isSystem();
}
