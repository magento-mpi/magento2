<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

/**
 * @codeCoverageIgnore
 */
class ValidationRule extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    /**
     * Constants used as keys into $_data
     */
    const NAME = 'name';

    const VALUE = 'value';

    /**
     * Get validation rule name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Get validation rule value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_get(self::VALUE);
    }
}
