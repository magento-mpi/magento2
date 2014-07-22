<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Data\ConfigurableAttribute;

use Magento\Framework\Service\Data\AbstractObjectBuilder;

class ValueBuilder extends AbstractObjectBuilder
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
    public function setPriceIsPercent($value)
    {
        return $this->_set(Value::PRICE_IS_PERCENT, $value);
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
