<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Validator\Constraint;

/**
 * Test case for \Magento\Framework\Validator\Constraint\Option
 */
class OptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test getValue
     */
    public function testGetValue()
    {
        $expected = 'test_value';
        $option = new \Magento\Framework\Validator\Constraint\Option($expected);
        $this->assertEquals($expected, $option->getValue());
    }
}
