<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto\Eav;

class ValidationRule extends \Magento\Service\Entity\AbstractDto
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
