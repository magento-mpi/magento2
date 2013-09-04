<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Filter_Template_SimpleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Filter_Template_Simple
     */
    protected $_filter;

    protected function setUp()
    {
        $this->_filter = new Magento_Filter_Template_Simple;
    }

    public function testFilter()
    {
        $template = 'My name is "{{first name}}" and my date of birth is {{dob}}.';
        $values = array(
            'first name' => 'User',
            'dob' => 'Feb 29, 2000',
        );
        $this->_filter->setData($values);
        $actual = $this->_filter->filter($template);
        $expected = 'My name is "User" and my date of birth is Feb 29, 2000.';
        $this->assertSame($expected, $actual);
    }

    public function testSetTags()
    {
        $this->_filter->setTags('(', ')');
        $this->_filter->setData(array(
            'pi' => '3.14',
        ));
        $template = 'PI = (pi)';
        $actual = $this->_filter->filter($template);
        $expected = 'PI = 3.14';
        $this->assertSame($expected, $actual);
    }
}
