<?php
namespace Magento\Eav\Api\Data;
use Magento\Framework\Service\Data\ExtensibleDataBuilder;

/**
 * DataBuilder class for \Magento\Eav\Api\Data\AttributeInterface
 */
class AttributeInterfaceDataBuilder extends \Magento\Framework\Service\Data\ExtensibleDataBuilder
{
    /**
     * @param string|null $attributeId
     */
    public function setAttributeId($attributeId)
    {
        $this->data['attribute_id'] = $attributeId;
        return $this;
    }

    /**
     * @param string|null $attributeCode
     */
    public function setAttributeCode($attributeCode)
    {
        $this->data['attribute_code'] = $attributeCode;
        return $this;
    }

    /**
     * @param string|null $frontendInput
     */
    public function setFrontendInput($frontendInput)
    {
        $this->data['frontend_input'] = $frontendInput;
        return $this;
    }

    /**
     * @param string $entityTypeId
     */
    public function setEntityTypeId($entityTypeId)
    {
        $this->data['entity_type_id'] = $entityTypeId;
        return $this;
    }

    /**
     * @param bool|null $isRequired
     */
    public function setIsRequired($isRequired)
    {
        $this->data['is_required'] = $isRequired;
        return $this;
    }

    /**
     * @param \Magento\Eav\Api\Data\AttributeOptionInterface $options
     */
    public function setOptions($options)
    {
        $this->data['options'] = $options;
        return $this;
    }

    /**
     * @param bool|null $isUserDefined
     */
    public function setIsUserDefined($isUserDefined)
    {
        $this->data['is_user_defined'] = $isUserDefined;
        return $this;
    }

    /**
     * @param mixed $frontendLabel
     */
    public function setFrontendLabel($frontendLabel)
    {
        $this->data['frontend_label'] = $frontendLabel;
        return $this;
    }

    /**
     * @param \Magento\Eav\Api\Data\AttributeFrontendLabelInterface
     * $storeFrontendLabels
     */
    public function setStoreFrontendLabels($storeFrontendLabels)
    {
        $this->data['store_frontend_labels'] = $storeFrontendLabels;
        return $this;
    }

    /**
     * @param string|null $note
     */
    public function setNote($note)
    {
        $this->data['note'] = $note;
        return $this;
    }

    /**
     * @param string|null $backendType
     */
    public function setBackendType($backendType)
    {
        $this->data['backend_type'] = $backendType;
        return $this;
    }

    /**
     * @param string|null $backendModel
     */
    public function setBackendModel($backendModel)
    {
        $this->data['backend_model'] = $backendModel;
        return $this;
    }

    /**
     * @param string|null $sourceModel
     */
    public function setSourceModel($sourceModel)
    {
        $this->data['source_model'] = $sourceModel;
        return $this;
    }

    /**
     * @param string|null $defaultValue
     */
    public function setDefaultValue($defaultValue)
    {
        $this->data['default_value'] = $defaultValue;
        return $this;
    }

    /**
     * @param string|null $isUnique
     */
    public function setIsUnique($isUnique)
    {
        $this->data['is_unique'] = $isUnique;
        return $this;
    }

    /**
     * @param string|null $scope
     */
    public function setScope($scope)
    {
        $this->data['scope'] = $scope;
        return $this;
    }

    /**
     * @param string|null $frontendClass
     */
    public function setFrontendClass($frontendClass)
    {
        $this->data['frontend_class'] = $frontendClass;
        return $this;
    }

    /**
     * @param \Magento\Eav\Api\Data\AttributeValidationRuleInterface $validationRules
     */
    public function setValidationRules($validationRules)
    {
        $this->data['validation_rules'] = $validationRules;
        return $this;
    }

    /**
     * Initialize the builder
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        parent::__construct($objectManager, 'Magento\Eav\Api\Data\AttributeInterface');
    }

    /**
     * Populates the fields with data from the array.
     *
     * Keys for the map are snake_case attribute/field names.
     *
     * @param array $data
     * @return $this
     */
    public function populateWithArray(array $data)
    {
        $this->data = [];
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }
        return $this;
    }
}
