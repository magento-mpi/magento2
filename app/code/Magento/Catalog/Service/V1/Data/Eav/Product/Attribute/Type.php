<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav\Product\Attribute;

/**
 * Class Type
 *
 * @package Magento\Catalog\Service\V1\Data\Eav\Product\Attribute
 * @codeCoverageIgnore
 */
class Type extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    /**
     * Constants used as keys into $_data
     */
    const LABEL = 'label';

    const VALUE = 'value';

    /**
     * Get option label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->_get(self::LABEL);
    }

    /**
     * Get option value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_get(self::VALUE);
    }
}
