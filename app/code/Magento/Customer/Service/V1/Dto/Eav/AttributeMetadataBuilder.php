<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto\Eav;

/**
 * Class AttributeMetadataBuilder
 */
class AttributeMetadataBuilder extends \Magento\Service\Entity\AbstractDtoBuilder
{
    /**
     * Option builder
     *
     * @var \Magento\Customer\Service\V1\Dto\Eav\OptionBuilder
     */
    protected $_optionBuilder;

    /**
     * Initializes builder.
     *
     * @param \Magento\Customer\Service\V1\Dto\Eav\OptionBuilder $optionBuilder
     */
    public function __construct(\Magento\Customer\Service\V1\Dto\Eav\OptionBuilder $optionBuilder)
    {
        parent::__construct();
        $this->_optionBuilder = $optionBuilder;
        $this->_data[AttributeMetadata::OPTIONS] = array();
    }

    /**
     * Set attribute code
     *
     * @param string $attributeCode
     * @return $this
     */
    public function setAttributeCode($attributeCode)
    {
        return $this->_set(AttributeMetadata::ATTRIBUTE_CODE, $attributeCode);
    }

    /**
     * Set front end input
     *
     * @param string $frontendInput
     * @return $this
     */
    public function setFrontendInput($frontendInput)
    {
        return $this->_set(AttributeMetadata::FRONT_END_INPUT, $frontendInput);
    }

    /**
     * Set input filter
     *
     * @param string $inputFilter
     * @return $this
     */
    public function setInputFilter($inputFilter)
    {
        return $this->_set(AttributeMetadata::INPUT_FILTER, $inputFilter);
    }

    /**
     * Set store label
     *
     * @param string $storeLabel
     * @return $this
     */
    public function setStoreLabel($storeLabel)
    {
        return $this->_set(AttributeMetadata::STORE_LABEL, $storeLabel);
    }

    /**
     * Set validation rules
     *
     * @param string $validationRules
     * @return $this
     */
    public function setValidationRules($validationRules)
    {
        return $this->_set(AttributeMetadata::VALIDATION_RULES, $validationRules);
    }

    /**
     * Set options
     *
     * @param \Magento\Customer\Service\V1\Dto\Eav\Option[] $options
     * @return $this
     */
    public function setOptions($options)
    {
        return $this->_set(AttributeMetadata::OPTIONS, $options);
    }

    /**
     * Set visible
     *
     * @param bool $visible
     * @return $this
     */
    public function setVisible($visible)
    {
        return $this->_set(AttributeMetadata::VISIBLE, $visible);
    }

    /**
     * Set required
     *
     * @param bool $required
     * @return $this
     */
    public function setRequired($required)
    {
        return $this->_set(AttributeMetadata::REQUIRED, $required);
    }


    /**
     * Set multiline count
     *
     * @param int $count
     * @return $this
     */
    public function setMultilineCount($count)
    {
        return $this->_set(AttributeMetadata::MULTILINE_COUNT, $count);
    }

    /**
     * Set data model
     *
     * @param string $dataModel
     * @return $this
     */
    public function setDataModel($dataModel)
    {
        return $this->_set(AttributeMetadata::DATA_MODEL, $dataModel);
    }

    /**
     * Set frontend class
     *
     * @param string $frontendClass
     * @return $this
     */
    public function setFrontendClass($frontendClass)
    {
        return $this->_set(AttributeMetadata::FRONTEND_CLASS, $frontendClass);
    }

    /**
     * Set is user defined
     * f
     * @param bool $isUserDefined
     * @return $this
     */
    public function setIsUserDefined($isUserDefined)
    {
        return $this->_set(AttributeMetadata::IS_USER_DEFINED, $isUserDefined);
    }

    /**
     * Set sort order
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {
        return $this->_set(AttributeMetadata::SORT_ORDER, $sortOrder);
    }

    /**
     * Set front end label
     *
     * @param string $frontendLabel
     * @return $this
     */
    public function setFrontendLabel($frontendLabel)
    {
        return $this->_set(AttributeMetadata::FRONTEND_LABEL, $frontendLabel);
    }

    /**
     * Set is system
     *
     * @param bool $isSystem
     * @return $this
     */
    public function setIsSystem($isSystem)
    {
        return $this->_set(AttributeMetadata::IS_SYSTEM, $isSystem);
    }

    /**
     * Set note
     *
     * @param string $note
     * @return $this
     */
    public function setNote($note)
    {
        return $this->_set(AttributeMetadata::NOTE, $note);
    }

    /**
     * {@inheritdoc}
     */
    public function populateWithArray(array $data)
    {
        if (array_key_exists(AttributeMetadata::OPTIONS, $data)) {
            $options = [];
            if (is_array($data[AttributeMetadata::OPTIONS])) {
                foreach ($data[AttributeMetadata::OPTIONS] as $key => $option) {
                    $options[$key] = $this->_optionBuilder->populateWithArray($option)->create();
                }
            }

            $data[AttributeMetadata::OPTIONS] = $options;
        }

        return parent::populateWithArray($data);
    }

    /**
     * Builds the entity.
     *
     * @return AttributeMetadata
     */
    public function create()
    {
        return parent::create();
    }
}
