<?php
/**
 * Eav Attribute Metadata
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\Entity\V1\Eav;

use Magento\Service\Entity\AbstractDto;
use Magento\Service\Entity\LazyArrayClone;

class AttributeMetadata extends AbstractDto
{
    /**
     * Constants used as keys into $_data
     */
    const ATTRIBUTE_CODE = 'attribute_code';
    const FRONT_END_INPUT = 'front_end_input';
    const INPUT_FILTER = 'input_filter';
    const STORE_LABEL = 'store_label';
    const VALIDATION_RULES = 'validation_rules';
    const OPTIONS = 'options';
    const VISIBLE = 'is_visible';
    const REQUIRED = 'is_required';
    const MULTILINE_COUNT = 'multiline_count';
    const DATA_MODEL = 'data_model';

    public function __construct()
    {
        parent::__construct();
        $this->_data[self::OPTIONS] = $this->_createArray();
    }

    /**
     * @return string
     */
    public function getAttributeCode()
    {
        return $this->_get(self::ATTRIBUTE_CODE);
    }

    /**
     * @return string
     */
    public function getFrontendInput()
    {
        return $this->_get(self::FRONT_END_INPUT);
    }

    /**
     * @return string
     */
    public function getInputFilter()
    {
        return $this->_get(self::INPUT_FILTER);
    }

    /**
     * @return string
     */
    public function getStoreLabel()
    {
        return $this->_get(self::STORE_LABEL);
    }

    /**
     * @return string
     */
    public function getValidationRules()
    {
        return $this->_get(self::VALIDATION_RULES);
    }

    /**
     * @return int
     */
    public function getMultilineCount()
    {
        return $this->_get(self::MULTILINE_COUNT);
    }

    /**
     * @return boolean
     */
    public function getIsVisible()
    {
        return $this->_get(self::VISIBLE);
    }

    /**
     * @return boolean
     */
    public function getIsRequired()
    {
        return $this->_get(self::REQUIRED);
    }

    /**
     * @return string
     */
    public function getDataModel()
    {
        return $this->_get(self::DATA_MODEL);
    }

    /**
     * @return Option[]
     */
    public function getOptions()
    {
        return $this->_get(self::OPTIONS);
    }

    /**
     * @param $attributeCode
     * @return AttributeMetadata
     */
    public function setAttributeCode($attributeCode)
    {
        return $this->_set(self::ATTRIBUTE_CODE, $attributeCode);
    }

    /**
     * @param $frontendInput
     * @return AttributeMetadata
     */
    public function setFrontendInput($frontendInput)
    {
        return $this->_set(self::FRONT_END_INPUT, $frontendInput);
    }

    /**
     * @param $inputFilter
     * @return AttributeMetadata
     */
    public function setInputFilter($inputFilter)
    {
        return $this->_set(self::INPUT_FILTER, $inputFilter);
    }

    /**
     * @param $storeLabel
     * @return AttributeMetadata
     */
    public function setStoreLabel($storeLabel)
    {
        return $this->_set(self::STORE_LABEL, $storeLabel);
    }

    /**
     * @param string $validationRules
     * @return AttributeMetadata
     */
    public function setValidationRules($validationRules)
    {
        return $this->_set(self::VALIDATION_RULES, $validationRules);
    }

    /**
     * @param \Magento\Customer\Service\Entity\V1\Eav\Option[] $options
     * @return AttributeMetadata
     */
    public function setOptions($options)
    {
        $this->_set(self::OPTIONS, $options);
    }

    /**
     * @param boolean $visible
     * @return AttributeMetadata
     */
    public function setIsVisible($visible)
    {
        return $this->_set(self::VISIBLE, $visible);
    }

    /**
     * @param boolean $required
     * @return AttributeMetadata
     */
    public function setIsRequired($required)
    {
        return $this->_set(self::REQUIRED, $required);
    }


    /**
     * @param int $count
     * @return AttributeMetadata
     */
    public function setMultilineCount($count)
    {
        return $this->_set(self::MULTILINE_COUNT, $count);
    }

    /**
     * @param string
     * @return AttributeMetadata
     */
    public function setDataModel($dataModel)
    {
        return $this->_set(self::DATA_MODEL, $dataModel);
    }
}
