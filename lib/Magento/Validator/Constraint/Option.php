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
     * @var mixed
     */
    protected $_value;

    /**
     * Set value
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->_value = $value;
    }

    /**
     * Get value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->_value;
    }
}
