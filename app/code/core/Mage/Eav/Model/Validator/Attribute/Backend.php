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
 * Validation EAV entity via EAV attributes' backend models
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Validator_Attribute_Backend implements Magento_Validator_ValidatorInterface
{
    /**
     * @var array
     */
    protected $_messages;

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * @param $entity
     * @return bool
     * @throws InvalidArgumentException
     */
    public function isValid($entity)
    {
        $this->_messages = array();
        /** @var $resource Mage_Eav_Model_Entity_Abstract */
        $resource = $entity->getResource();
        if (!($resource instanceof Mage_Eav_Model_Entity_Abstract)) {
            throw new InvalidArgumentException('Model resource must be extended from Mage_Eav_Model_Entity_Abstract');
        }
        $resource->loadAllAttributes($entity);
        $attributes = $resource->getAttributesByCode();
        /** @var $attribute Mage_Eav_Model_Entity_Attribute */
        foreach ($attributes as $attribute) {
            $backend = $attribute->getBackend();
            if (!method_exists($backend, 'validate')) {
                continue;
            }
            try {
                $result = $backend->validate($entity);
                if (false === $result) {
                    $this->_messages[$attribute->getAttributeCode()][] =
                        Mage::helper('Mage_Eav_Helper_Data')->__('The value of attribute "%s" is invalid',
                            $attribute->getAttributeCode());
                } elseif (is_string($result)) {
                    $this->_messages[$attribute->getAttributeCode()][] = $result;
                }
            } catch (Mage_Core_Exception $e) {
                $this->_messages[$attribute->getAttributeCode()][] = $e->getMessage();
            }
        }
        return 0 == count($this->_messages);
    }

    /**
     * Returns an array of messages that explain why the most recent isValid()
     * call returned false.
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }
}
