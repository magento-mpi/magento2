<?php
/**
 * Form Element Text Data Model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Metadata\Form;

class Text extends AbstractData
{
    /**
     * @var \Magento\Stdlib\String
     */
    protected $_string;

    /**
     * @param \Magento\LocaleInterface $locale
     * @param \Magento\Logger $logger
     * @param \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata $attribute
     * @param string $value
     * @param string $entityTypeCode
     * @param bool $isAjax
     * @param \Magento\Stdlib\String $stringHelper
     */
    public function __construct(
        \Magento\LocaleInterface $locale,
        \Magento\Logger $logger,
        \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata $attribute,
        $value,
        $entityTypeCode,
        $isAjax = false,
        \Magento\Stdlib\String $stringHelper
    ) {
        parent::__construct($locale, $logger, $attribute, $value, $entityTypeCode, $isAjax);
        $this->_string = $stringHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function extractValue(\Magento\App\RequestInterface $request)
    {
        return $this->_applyInputFilter($this->_getRequestValue($request));
    }

    /**
     * {@inheritdoc}
     */
    public function validateValue($value)
    {
        $errors     = array();
        $attribute  = $this->getAttribute();
        $label      = __($attribute->getStoreLabel());

        if ($value === false) {
            // try to load original value and validate it
            $value = $this->_value;
        }

        if ($attribute->isRequired() && empty($value) && $value !== '0') {
            $errors[] = __('"%1" is a required value.', $label);
        }

        if (!$errors && !$attribute->isRequired() && empty($value)) {
            return true;
        }

        // validate length
        $length = $this->_string->strlen(trim($value));

        $validateRules = $attribute->getValidationRules();
        if (!empty($validateRules['min_text_length']) && $length < $validateRules['min_text_length']) {
            $v = $validateRules['min_text_length'];
            $errors[] = __('"%1" length must be equal or greater than %2 characters.', $label, $v);
        }
        if (!empty($validateRules['max_text_length']) && $length > $validateRules['max_text_length']) {
            $v = $validateRules['max_text_length'];
            $errors[] = __('"%1" length must be equal or less than %2 characters.', $label, $v);
        }

        $result = $this->_validateInputRule($value);
        if ($result !== true) {
            $errors = array_merge($errors, $result);
        }
        if (count($errors) == 0) {
            return true;
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function compactValue($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function restoreValue($value)
    {
        return $this->compactValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function outputValue($format = \Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_TEXT)
    {
        return $this->_applyOutputFilter($this->_value);
    }
}
