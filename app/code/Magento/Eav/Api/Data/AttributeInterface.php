<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api\Data;

interface AttributeInterface 
{
    /**
     * Retrieve id of the attribute.
     *
     * @return string|null
     */
    public function getAttributeId();

    /**
     * Retrieve code of the attribute.
     *
     * @return string|null
     */
    public function getAttributeCode();

    /**
     * Frontend HTML for input element.
     *
     * @return string|null
     */
    public function getFrontendInput();

    /**
     * Whether attribute is required.
     *
     * @return bool|null
     */
    public function isRequired();

    /**
     * Return options of the attribute (key => value pairs for select)
     *
     * @return \Magento\Eav\Api\Data\AttributeOptionInterface[]|null
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
     * @return string|null
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
     * Retrieve validation rules.
     *
     * @return \Magento\Eav\Api\Data\AttributeValidationRuleInterface[]|null
     */
    public function getValidationRules();
}
