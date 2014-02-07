<?php
/**
 * Attribute data validator
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Metadata;

class Validator extends \Magento\Eav\Model\Validator\Attribute\Data
{
    /**
     * @var string
     */
    protected $_entityType;

    /**
     * @var array
     */
    protected $_entityData;

    /**
     * @param \Magento\Customer\Model\Metadata\ElementFactory $attrDataFactory
     */
    public function __construct(\Magento\Customer\Model\Metadata\ElementFactory $attrDataFactory)
    {
        $this->_attrDataFactory = $attrDataFactory;
    }

    /**
     * Validate EAV model attributes with data models
     *
     * @param \Magento\Object|array $entityData Data set from the Model attributes
     * @return bool
     */
    public function isValid($entityData)
    {
        if ($entityData instanceof \Magento\Object) {
            $this->_entityData = $entityData->getData();
        } else {
            $this->_entityData = $entityData;
        }
        //$this->_data refers to the data being passed for validation
        $this->validateData($this->_data, $this->_attributes, $this->_entityType);
    }

    public function validateData($data, $attributes, $entityType)
    {
        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            if (!$attribute->getDataModel() && !$attribute->getFrontendInput()) {
                continue;
            }
            if (!isset($data[$attributeCode])) {
                $data[$attributeCode] = null;
            }
            $dataModel = $this->_attrDataFactory->create(
                $attribute, $data[$attributeCode], $entityType
            );
            $dataModel->setExtractedData($data);
            $value = empty($data[$attributeCode]) && isset($this->_entityData[$attributeCode])
                ? $this->_entityData[$attributeCode]
                : $data[$attributeCode];
            $result = $dataModel->validateValue($value);
            if (true !== $result) {
                $this->_addErrorMessages($attributeCode, (array)$result);
            }
        }
        return count($this->_messages) == 0;
    }

    /**
     * Set type of the entity
     *
     * @param string $entityType
     * @return null
     */
    public function setEntityType($entityType)
    {
        $this->_entityType = $entityType;
    }
}
