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
 * EAV model validator
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Validator
{
    /**
     * @var Mage_Core_Model_Abstract
     */
    protected $_entity;

    /**
     * @var Mage_Eav_Model_Entity_Attribute_Abstract
     */
    protected $_attribute;

    /**
     * @var mixed
     */
    protected $_value;

    /**
     * @var array
     */
    protected $_messages = array();

    /**
     * Check entity is valid
     *
     * @param Mage_Core_Model_Abstract $entity
     * @param array|Traversable|null $attributes
     * @throws InvalidArgumentException
     * @return boolean
     */
    public function isValid(Mage_Core_Model_Abstract $entity, $attributes = null)
    {
        $this->_messages = array();

        /** @var $resource Mage_Eav_Model_Entity_Abstract */
        $resource = $entity->getResource();
        if (!($resource instanceof Mage_Eav_Model_Entity_Abstract)) {
            throw new InvalidArgumentException('Model resource must be extended from Mage_Eav_Model_Entity_Abstract');
        }

        $this->_entity = $entity;

        if ($attributes && !is_array($attributes) && !($attributes instanceof Traversable)) {
            throw new InvalidArgumentException('$attributes should be an array or instance of Traversable');
        } else {
            $resource->loadAllAttributes($entity);
            $attributes = $resource->getAttributesByCode();
        }

        /** @var $attribute Mage_Eav_Model_Entity_Attribute */
        foreach ($attributes as $attribute) {
            $this->_attribute = $attribute;
            $this->_value = $this->_entity->getDataUsingMethod($attribute->getAttributeCode());
            $this->_validateDataModel();
            $this->_validateBackend();
        }
        unset($this->_attribute);
        unset($this->_value);

        return 0 == count($this->_messages);
    }


    /**
     * If attribute has data model, apply it's validation
     */
    protected function _validateDataModel()
    {
        if (!$this->_attribute->getDataModel() && !$this->_attribute->getFrontendInput()) {
            return;
        }

        $dataModel = Mage_Eav_Model_Attribute_Data::factory($this->_attribute, $this->_entity);
        $result = $dataModel->validateValue($this->_value);

        if ($result !== true) {
            $this->_addMessages((array)$result);
        }
    }

    /**
     * If attribute has backend model validation method, apply it
     */
    protected function _validateBackend()
    {
        $backend = $this->_attribute->getBackend();
        if (!method_exists($backend, 'validate')) {
            return;
        }

        try {
            $result = $backend->validate($this->_entity);
            if (false === $result) {
                $this->_addMessages(array(
                    Mage::helper('Mage_Eav_Helper_Data')->__('The value of attribute "%s" is invalid',
                        $this->_attribute->getAttributeCode())
                ));
            } elseif (is_string($result)) {
                $this->_addMessages(array($result));
            }
        } catch (Mage_Core_Exception $e) {
            $this->_addMessages(array($e->getMessage()));
        }
    }

    /**
     * Add messages with validation errors
     *
     * @param array $messages
     */
    protected function _addMessages(array $messages)
    {
        $attributeCode = $this->_attribute->getAttributeCode();
        if (!isset($this->_messages[$attributeCode])) {
            $this->_messages[$attributeCode] = array();
        }
        $this->_messages[$attributeCode] = array_merge($this->_messages[$attributeCode], $messages);
    }

    /**
     * Get validation errors messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }
}
