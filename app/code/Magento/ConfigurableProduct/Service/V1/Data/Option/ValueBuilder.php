<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Data\Option;

use Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder;

/**
 * @codeCoverageIgnore
 */
class ValueBuilder extends AbstractExtensibleObjectBuilder
{
    /**
     * @param float $value 
     * @return self 
     */
    public function setPrice($value)
    {
        return $this->_set(Value::PRICE, $value);
    }

    /**
     * @param int $value 
     * @return self 
     */
    public function setPercent($value)
    {
        return $this->_set(Value::IS_PERCENT, $value);
    }

    /**
     * @param int $value
     * @return self
     */
    public function setIndex($value)
    {
        return $this->_set(Value::INDEX, $value);
    }
}
