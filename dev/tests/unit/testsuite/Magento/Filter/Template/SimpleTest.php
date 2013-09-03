<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Filter_Template_SimpleTest extends PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $template = 'My name is "{{first name}}" and my date of birth is {{dob}}.';
        $values = array(
            'first name' => 'User',
            'dob' => 'Feb 29, 2000',
        );
        $filter = new Magento_Filter_Template_Simple;
        $filter->setData($values);
        $actual = $filter->filter($template);
        $expected = 'My name is "User" and my date of birth is Feb 29, 2000.';
        $this->assertSame($expected, $actual);
    }
}
