<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Service\V1\Data\Option;

class Value extends \Magento\Framework\Service\Data\AbstractObject
{
    const INDEX = 'value_index';
    const PRICE = 'pricing_value';
    const PRICE_IS_PERCENT = 'is_percent';

    /**
     * @return float|null
     */
    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    /**
     * @return int|null
     */
    public function getPriceIsPercent()
    {
        return $this->_get(self::PRICE_IS_PERCENT);
    }

    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->_get(self::INDEX);
    }
}
