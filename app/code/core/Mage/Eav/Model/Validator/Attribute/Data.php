<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * EAV attribute data validator
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Validator_Attribute_Data implements Magento_Validator_Interface
{
    /**
     * @var array
     */
    protected $_messages = array();

    /**
     * @var array|Varien_Data_Collection
     */
    protected $_attributes;

    /**
     * @var array
     */
    protected $_data;

    /**
     * Set attributes collection
     *
     * @param array|Varien_Data_Collection $attributes
     */
    public function setAttributes($attributes)
    {
        $this->_attributes = $attributes;
    }

    /**
     * Set extracted data
     *
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->_data = $data;
    }

    /**
     * Validate EAV model attributes with data models
     *
     * @param Mage_Core_Model_Abstract $entity
     * @return bool
     */
    public function isValid($entity)
    {
        /** @var $attribute Mage_Eav_Model_Attribute */
        foreach ($this->_attributes as $attribute) {
            $dataModel = $this->_getAttributeDataModel($attribute, $entity);
            $dataModel->setExtractedData($this->_data);
            if (!isset($this->_data[$attribute->getAttributeCode()])) {
                $this->_data[$attribute->getAttributeCode()] = null;
            }
            $result = $dataModel->validateValue($this->_data[$attribute->getAttributeCode()]);
            if (true !== $result) {
                $this->_addErrorMessages($attribute->getAttributeCode(), (array)$result);
            }
        }
        return count($this->_messages) == 0;
    }

    /**
     * Get attribute data model
     *
     * @param Mage_Eav_Model_Attribute $attribute
     * @param Mage_Core_Model_Abstract $entity
     * @return Mage_Eav_Model_Attribute_Data_Abstract
     */
    protected function _getAttributeDataModel($attribute, $entity)
    {
        return Mage_Eav_Model_Attribute_Data::factory($attribute, $entity);
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

    /**
     * Get validation messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }
}
