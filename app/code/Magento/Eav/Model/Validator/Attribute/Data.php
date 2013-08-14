<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * EAV attribute data validator
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Eav_Model_Validator_Attribute_Data extends Magento_Validator_ValidatorAbstract
{
    /**
     * @var array
     */
    protected $_attributes = array();

    /**
     * @var array
     */
    protected $_attributesWhiteList = array();

    /**
     * @var array
     */
    protected $_attributesBlackList = array();

    /**
     * @var array
     */
    protected $_data = array();

    /**
     * @var Magento_Eav_Model_Attribute_Data
     */
    protected $_dataModelFactory;

    /**
     * Set list of attributes for validation in isValid method.
     *
     * @param Magento_Eav_Model_Attribute[] $attributes
     * @return Magento_Eav_Model_Validator_Attribute_Data
     */
    public function setAttributes(array $attributes)
    {
        $this->_attributes = $attributes;
        return $this;
    }

    /**
     * Set codes of attributes that should be filtered in validation process.
     *
     * All attributes not in this list 't be involved in validation.
     *
     * @param array $attributesCodes
     * @return Magento_Eav_Model_Validator_Attribute_Data
     */
    public function setAttributesWhiteList(array $attributesCodes)
    {
        $this->_attributesWhiteList = $attributesCodes;
        return $this;
    }

    /**
     * Set codes of attributes that should be excluded in validation process.
     *
     * All attributes in this list won't be involved in validation.
     *
     * @param array $attributesCodes
     * @return Magento_Eav_Model_Validator_Attribute_Data
     */
    public function setAttributesBlackList(array $attributesCodes)
    {
        $this->_attributesBlackList = $attributesCodes;
        return $this;
    }

    /**
     * Set data for validation in isValid method.
     *
     * @param array $data
     * @return Magento_Eav_Model_Validator_Attribute_Data
     */
    public function setData(array $data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * Validate EAV model attributes with data models
     *
     * @param Magento_Core_Model_Abstract $entity
     * @return bool
     */
    public function isValid($entity)
    {
        /** @var $attributes Magento_Eav_Model_Attribute[] */
        $attributes = $this->_getAttributes($entity);

        $data = array();
        if ($this->_data) {
            $data = $this->_data;
        } elseif ($entity instanceof Magento_Object) {
            $data = $entity->getData();
        }

        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            if (!$attribute->getDataModel() && !$attribute->getFrontendInput()) {
                continue;
            }
            $dataModel = $this->getAttributeDataModelFactory()->factory($attribute, $entity);
            $dataModel->setExtractedData($data);
            if (!isset($data[$attributeCode])) {
                $data[$attributeCode] = null;
            }
            $result = $dataModel->validateValue($data[$attributeCode]);
            if (true !== $result) {
                $this->_addErrorMessages($attributeCode, (array)$result);
            }
        }
        return count($this->_messages) == 0;
    }

    /**
     * Get attributes involved in validation.
     *
     * This method return specified $_attributes if they defined by setAttributes method, otherwise if $entity
     * is EAV-model it returns it's all available attributes, otherwise it return empty array.
     *
     * @param mixed $entity
     * @return array
     */
    protected function _getAttributes($entity)
    {
        /** @var Magento_Customer_Model_Attribute[] $attributes */
        $attributes = array();

        if ($this->_attributes) {
            $attributes = $this->_attributes;
        } elseif ($entity instanceof Magento_Core_Model_Abstract
                  && $entity->getResource() instanceof Magento_Eav_Model_Entity_Abstract
        ) { // $entity is EAV-model
            $attributes = $entity->getEntityType()->getAttributeCollection()->getItems();
        }

        $attributesByCode = array();
        $attributesCodes = array();
        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $attributesByCode[$attributeCode] = $attribute;
            $attributesCodes[] = $attributeCode;
        }

        $ignoreAttributes = $this->_attributesBlackList;
        if ($this->_attributesWhiteList) {
            $ignoreAttributes = array_merge(
                $ignoreAttributes,
                array_diff($attributesCodes, $this->_attributesWhiteList)
            );
        }

        foreach ($ignoreAttributes as $attributeCode) {
            unset($attributesByCode[$attributeCode]);
        }

        return $attributesByCode;
    }

    /**
     * Get factory object for creating Attribute Data Model
     *
     * @return Magento_Eav_Model_Attribute_Data
     */
    public function getAttributeDataModelFactory()
    {
        if (!$this->_dataModelFactory) {
            $this->_dataModelFactory = new Magento_Eav_Model_Attribute_Data;
        }
        return $this->_dataModelFactory;
    }

    /**
     * Set factory object for creating Attribute Data Model
     *
     * @param Magento_Eav_Model_Attribute_Data $factory
     * @return Magento_Eav_Model_Validator_Attribute_Data
     */
    public function setAttributeDataModelFactory($factory)
    {
        $this->_dataModelFactory = $factory;
        return $this;
    }

    /**
     * Add error messages
     *
     * @param string $code
     * @param array $messages
     */
    protected function _addErrorMessages($code, array $messages)
    {
        if (!array_key_exists($code, $this->_messages)) {
            $this->_messages[$code] = $messages;
        } else {
            $this->_messages[$code] = array_merge($this->_messages[$code], $messages);
        }
    }
}
