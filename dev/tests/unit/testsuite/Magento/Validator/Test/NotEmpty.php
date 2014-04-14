<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Validator
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Not empty test validator
 */
namespace Magento\Validator\Test;

class NotEmpty extends \Zend_Validate_NotEmpty implements \Magento\Validator\ValidatorInterface
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
