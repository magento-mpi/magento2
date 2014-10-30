<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Data;

/**
 * Customer attribute metadata class.
 */
class AttributeMetadata extends \Magento\Framework\Service\Data\AbstractExtensibleObject implements
    \Magento\Customer\Api\Data\AttributeMetadataInterface
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

    const USER_DEFINED = 'user_defined';

    const FRONTEND_CLASS = 'frontend_class';

    const SORT_ORDER = 'sort_order';

    const FRONTEND_LABEL = 'frontend_label';

    const SYSTEM = 'system';

    const NOTE = 'note';

    const BACKEND_TYPE = 'backend_type';

    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function getAttributeCode()
    {
        return $this->_get(self::ATTRIBUTE_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function getFrontendInput()
    {
        return $this->_get(self::FRONTEND_INPUT);
    }

    /**
     * {@inheritdoc}
     */
    public function getInputFilter()
    {
        return $this->_get(self::INPUT_FILTER);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreLabel()
    {
        return $this->_get(self::STORE_LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationRules()
    {
        return $this->_get(self::VALIDATION_RULES);
    }

    /**
     * {@inheritdoc}
     */
    public function getMultilineCount()
    {
        return $this->_get(self::MULTILINE_COUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function isVisible()
    {
        return $this->_get(self::VISIBLE);
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired()
    {
        return $this->_get(self::REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    public function getDataModel()
    {
        return $this->_get(self::DATA_MODEL);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->_get(self::OPTIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function getFrontendClass()
    {
        return $this->_get(self::FRONTEND_CLASS);
    }

    /**
     * {@inheritdoc}
     */
    public function isUserDefined()
    {
        return $this->_get(self::USER_DEFINED);
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return $this->_get(self::SORT_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function getFrontendLabel()
    {
        return $this->_get(self::FRONTEND_LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function getNote()
    {
        return $this->_get(self::NOTE);
    }

    /**
     * {@inheritdoc}
     */
    public function isSystem()
    {
        return $this->_get(self::SYSTEM);
    }

    /**
     * {@inheritdoc}
     */
    public function getBackendType()
    {
        return $this->_get(self::BACKEND_TYPE);
    }
}
