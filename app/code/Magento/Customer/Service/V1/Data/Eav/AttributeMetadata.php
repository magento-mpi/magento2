<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data\Eav;

/**
 * Class AttributeMetadata
 */
class AttributeMetadata extends \Magento\Service\Data\AbstractObject
{
    /**#@+
     * Constants used as keys into $_data
     */
    const ATTRIBUTE_CODE = 'attribute_code';
    const FRONTEND_INPUT = 'frontend_input';
    const INPUT_FILTER = 'input_filter';
    const STORE_LABEL = 'store_label';
    const VALIDATION_RULES = 'validation_rules';
    const OPTIONS = 'options';
    const VISIBLE = 'visible';
    const REQUIRED = 'required';
    const MULTILINE_COUNT = 'multiline_count';
    const DATA_MODEL = 'data_model';
    const IS_USER_DEFINED = 'is_user_defined';
    const FRONTEND_CLASS = 'frontend_class';
    const SORT_ORDER = 'sort_order';
    const FRONTEND_LABEL = 'frontend_label';
    const IS_SYSTEM = 'is_system';
    const NOTE = 'note';
    const BACKEND_TYPE = 'backend_type';
    /**#@-*/

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
     * Get template used for input (e.g. "date")
     *
     * @return string
     */
    public function getInputFilter()
    {
        return $this->_get(self::INPUT_FILTER);
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
     * @return \Magento\Customer\Service\V1\Dto\Eav\ValidationRule[]
     */
    public function getValidationRules()
    {
        return $this->_get(self::VALIDATION_RULES);
    }

    /**
     * Number of lines of the attribute value.
     *
     * @return int
     */
    public function getMultilineCount()
    {
        return $this->_get(self::MULTILINE_COUNT);
    }

    /**
     * Whether attribute is visible on frontend.
     *
     * @return bool
     */
    public function isVisible()
    {
        return $this->_get(self::VISIBLE);
    }

    /**
     * Whether attribute is required.
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->_get(self::REQUIRED);
    }

    /**
     * Get data model for attribute.
     *
     * @return string
     */
    public function getDataModel()
    {
        return $this->_get(self::DATA_MODEL);
    }

    /**
     * Return options of the attribute (key => value pairs for select)
     *
     * @return \Magento\Customer\Service\V1\Data\Eav\Option[]
     */
    public function getOptions()
    {
        return $this->_get(self::OPTIONS);
    }

    /**
     * Get class which is used to display the attribute on frontend.
     *
     * @return string
     */
    public function getFrontendClass()
    {
        return $this->_get(self::FRONTEND_CLASS);
    }

    /**
     * Whether current attribute has been defined by a user.
     *
     * @return bool
     */
    public function isUserDefined()
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
    public function isSystem()
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
}
