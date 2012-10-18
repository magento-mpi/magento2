<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Validator
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Alphanumerical validator
 */
class Magento_Validator_Entity_Properties extends Magento_Validator_ValidatorAbstract
{
    /**
     * @var array
     */
    protected $_readOnlyProperties = array();

    /**
     * @param array $readOnlyProperties
     */
    public function setReadOnlyProperties(array $readOnlyProperties)
    {
        $this->_readOnlyProperties = $readOnlyProperties;
    }

    /**
     * Successful if $value is Varien_Object an all condition are fulfilled.
     *
     * If properties were set by setReadOnlyProperties than $value mustn't have changes in them.
     *
     * @param Varien_Object|mixed $value
     * @return bool
     * @throws InvalidArgumentException when $value is not instanceof Varien_Object
     */
    public function isValid($value)
    {
        $this->_clearMessages();
        if (!($value instanceof Varien_Object)) {
            throw new InvalidArgumentException('Instance of Varien_Object is expected.');
        }
        if ($this->_readOnlyProperties) {
            if (!$value->hasDataChanges()) {
                return true;
            }
            foreach ($this->_readOnlyProperties as $property) {
                if ($value->getData($property) !== $value->getOrigData($property)) {
                    // @todo Add string translation (MAGETWO-3988)
                    $this->_messages[__CLASS__] = array("Read only property cannot be changed.");
                    break;
                }
            }
        }
        return !count($this->_messages);
    }
}
