<?php
/**
 * Eav attribute option
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto\Eav;

class Option extends \Magento\Service\Entity\AbstractDto
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
