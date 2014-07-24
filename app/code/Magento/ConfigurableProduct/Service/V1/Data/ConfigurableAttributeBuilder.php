<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Data;

class ConfigurableAttributeBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * @param int $value 
     * @return self 
     */
    public function setId($value)
    {
        return $this->_set(ConfigurableAttribute::ID, $value);
    }

    /**
     * @param string $value
     * @return self
     */
    public function setAttributeId($value)
    {
        return $this->_set(ConfigurableAttribute::ATTRIBUTE_ID, $value);
    }

    /**
     * @param string $value
     * @return self
     */
    public function setAttributeCode($value)
    {
        return $this->_set(ConfigurableAttribute::ATTRIBUTE_CODE, $value);
    }

    /**
     * @param string $value 
     * @return self 
     */
    public function setLabel($value)
    {
        return $this->_set(ConfigurableAttribute::LABEL, $value);
    }

    /**
     * @param bool $value 
     * @return self 
     */
    public function useDefault($value)
    {
        return $this->_set(ConfigurableAttribute::USE_DEFAULT, $value);
    }

    /**
     * @param \Magento\ConfigurableProduct\Service\V1\Data\ConfigurableAttribute\Value[] $value 
     * @return self 
     */
    public function setValues($value)
    {
        return $this->_set(ConfigurableAttribute::VALUES, $value);
    }
}
