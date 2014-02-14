<?php
/**
 * Constraint option
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Validator\Constraint;

class Option implements  \Magento\Validator\Constraint\OptionInterface
{
    /**
     * @var int|string|array
     */
    protected $_value;

    /**
     * Set value
     *
     * @param int|string|array $value
     */
    public function __construct($value)
    {
        $this->_value = $value;
    }

    /**
     * Get value
     *
     * @return int|string|array
     */
    public function getValue()
    {
        return $this->_value;
    }
}
