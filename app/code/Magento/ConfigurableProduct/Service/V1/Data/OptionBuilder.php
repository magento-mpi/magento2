<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Data;

use Magento\Framework\Service\Data\AbstractObjectBuilder;

class OptionBuilder extends AbstractObjectBuilder
{
    /**
     * @param int $value
     * @return $this
     */
    public function setId($value)
    {
        return $this->_set(Option::ID, $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setAttributeId($value)
    {
        return $this->_set(Option::ATTRIBUTE_ID, $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setLabel($value)
    {
        return $this->_set(Option::LABEL, $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setType($value)
    {
        return $this->_set(Option::TYPE, $value);
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function useDefault($value)
    {
        return $this->_set(Option::USE_DEFAULT, $value);
    }

    /**
     * @param \Magento\ConfigurableProduct\Service\V1\Data\Option\Value[] $value
     * @return $this
     */
    public function setValues($value)
    {
        return $this->_set(Option::VALUES, $value);
    }
}
