<?php
/**
 * Validates properties of entity (\Magento\Object).
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Validator\Entity;

use Magento\Object;

class Properties extends \Magento\Framework\Validator\AbstractValidator
{
    /**
     * @var string[]
     */
    protected $_readOnlyProperties = array();

    /**
     * Set read-only properties.
     *
     * @param string[] $readOnlyProperties
     * @return void
     */
    public function setReadOnlyProperties(array $readOnlyProperties)
    {
        $this->_readOnlyProperties = $readOnlyProperties;
    }

    /**
     * Successful if $value is \Magento\Object an all condition are fulfilled.
     *
     * If read-only properties are set than $value mustn't have changes in them.
     *
     * @param Object $value
     * @return bool
     * @throws \InvalidArgumentException when $value is not instanceof \Magento\Object
     */
    public function isValid($value)
    {
        $this->_clearMessages();
        if (!$value instanceof Object) {
            throw new \InvalidArgumentException('Instance of \Magento\Object is expected.');
        }
        if ($this->_readOnlyProperties) {
            if (!$value->hasDataChanges()) {
                return true;
            }
            foreach ($this->_readOnlyProperties as $property) {
                if ($this->_hasChanges($value->getData($property), $value->getOrigData($property))) {
                    $this->_messages[__CLASS__] = array(__("Read-only property cannot be changed."));
                    break;
                }
            }
        }
        return !count($this->_messages);
    }

    /**
     * Compare two values as numbers and as other types
     *
     * @param mixed $firstValue
     * @param mixed $secondValue
     * @return bool
     */
    protected function _hasChanges($firstValue, $secondValue)
    {
        if ($firstValue === $secondValue || $firstValue == $secondValue && is_numeric(
            $firstValue
        ) && is_numeric(
            $secondValue
        )
        ) {
            return false;
        }
        return true;
    }
}
