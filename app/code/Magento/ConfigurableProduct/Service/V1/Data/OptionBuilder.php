<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Data;

class OptionBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * @param int $value 
     * @return self 
     */
    public function setId($value)
    {
        return $this->_set(Option::ID, $value);
    }

    /**
     * @param string $value
     * @return self
     */
    public function setAttributeId($value)
    {
        return $this->_set(Option::ATTRIBUTE_ID, $value);
    }

    /**
     * @param string $value 
     * @return self 
     */
    public function setLabel($value)
    {
        return $this->_set(Option::LABEL, $value);
    }

    /**
     * @param bool $value 
     * @return self 
     */
    public function useDefault($value)
    {
        return $this->_set(Option::USE_DEFAULT, $value);
    }

    /**
     * @param \Magento\ConfigurableProduct\Service\V1\Data\Option\Value[] $value
     * @return self 
     */
    public function setValues($value)
    {
        return $this->_set(Option::VALUES, $value);
    }
}
