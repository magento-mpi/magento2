<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Api\Data;

/**
 * Customer attribute metadata interface.
 */
interface AttributeMetadata
{
    /**
     * Retrieve code of the attribute.
     *
     * @return string
     */
    public function getAttributeCode();

    /**
     * Frontend HTML for input element.
     *
     * @return string
     */
    public function getFrontendInput();

    /**
     * Get template used for input (e.g. "date")
     *
     * @return string
     */
    public function getInputFilter();

    /**
     * Get label of the store.
     *
     * @return string
     */
    public function getStoreLabel();

    /**
     * Retrieve validation rules.
     *
     * @return \Magento\Customer\Service\V1\Data\Eav\ValidationRule[]
     */
    public function getValidationRules();

    /**
     * Number of lines of the attribute value.
     *
     * @return int
     */
    public function getMultilineCount();

    /**
     * Whether attribute is visible on frontend.
     *
     * @return bool
     */
    public function isVisible();

    /**
     * Whether attribute is required.
     *
     * @return bool
     */
    public function isRequired();

    /**
     * Get data model for attribute.
     *
     * @return string
     */
    public function getDataModel();

    /**
     * Return options of the attribute (key => value pairs for select)
     *
     * @return \Magento\Customer\Service\V1\Data\Eav\Option[]
     */
    public function getOptions();

    /**
     * Get class which is used to display the attribute on frontend.
     *
     * @return string
     */
    public function getFrontendClass();

    /**
     * Whether current attribute has been defined by a user.
     *
     * @return bool
     */
    public function isUserDefined();

    /**
     * Get attributes sort order.
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * Get label which supposed to be displayed on frontend.
     *
     * @return string
     */
    public function getFrontendLabel();

    /**
     * Get the note attribute for the element.
     *
     * @return string
     */
    public function getNote();

    /**
     * Whether this is a system attribute.
     *
     * @return bool
     */
    public function isSystem();

    /**
     * Get backend type.
     *
     * @return string
     */
    public function getBackendType();
}
