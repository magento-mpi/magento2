<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Validator\Test;

/**
 * Not empty test validator
 */
class NotEmpty extends \Zend_Validate_NotEmpty implements \Magento\Framework\Validator\ValidatorInterface
{
    /**
     * Custom constructor.
     * Needed because parent Zend class has the bug - when default value NULL is passed to the constructor,
     * then it throws the exception.
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
    }
}
