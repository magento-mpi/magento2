<?php
/**
 * Eav attribute option
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data\Eav;

/**
 * Class Option
 */
class Option extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    /**
     * Constants used as keys into $_data
     */
    const LABEL = 'label';

    const VALUE = 'value';

    const OPTIONS = 'options';

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
     * @return string|null
     */
    public function getValue()
    {
        return $this->_get(self::VALUE);
    }

    /**
     * Get nested options
     *
     * @return \Magento\Customer\Service\V1\Data\Eav\Option[]|null
     */
    public function getOptions()
    {
        return $this->_get(self::OPTIONS);
    }
}
