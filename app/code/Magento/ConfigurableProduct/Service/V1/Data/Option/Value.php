<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Data\Option;

/**
 * @codeCoverageIgnore
 */
class Value extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    const INDEX = 'index';
    const PRICE = 'price';
    const IS_PERCENT = 'percent';

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
    public function isPercent()
    {
        return $this->_get(self::IS_PERCENT);
    }

    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->_get(self::INDEX);
    }
}
