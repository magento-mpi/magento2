<?php
/**
 * Eav attribute option
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\Entity\V1\Eav;

use Magento\Service\Entity\AbstractDto;

class Option extends AbstractDto
{
    /**
     * Constants used as keys into $_data
     */
    const LABEL = 'label';
    const VALUE = 'value';

    /**
     * Constructor
     *
     * @param string $label
     * @param string $value
     */
    public function __construct($label, $value)
    {
        parent::__construct();
        $this->_set(self::LABEL, $label);
        $this->_set(self::VALUE, $value);
    }

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
