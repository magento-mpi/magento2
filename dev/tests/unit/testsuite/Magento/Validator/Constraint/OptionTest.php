<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Validator
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test case for \Magento\Validator\Constraint\Option
 */
class Magento_Validator_Constraint_OptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test getValue
     */
    public function testGetValue()
    {
        $expected = 'test_value';
        $option = new \Magento\Validator\Constraint\Option($expected);
        $this->assertEquals($expected, $option->getValue());
    }
}
