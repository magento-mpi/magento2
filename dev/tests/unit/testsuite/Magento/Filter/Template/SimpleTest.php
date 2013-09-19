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
     * @var \Magento\Filter\Template\Simple
     */
    protected $_filter;

    protected function setUp()
    {
        $this->_filter = new \Magento\Filter\Template\Simple;
    }

    public function testFilter()
    {
        $template = 'My name is "{{first name}}" and my date of birth is {{dob}}.';
        $values = array(
            'first name' => 'User',
            'dob'        => 'Feb 29, 2000',
        );
        $this->_filter->setData($values);
        $actual = $this->_filter->filter($template);
        $expected = 'My name is "User" and my date of birth is Feb 29, 2000.';
        $this->assertSame($expected, $actual);
    }

    /**
     * @param string $startTag
     * @param string $endTag
     * @dataProvider setTagsDataProvider
     */
    public function testSetTags($startTag, $endTag)
    {
        $this->_filter->setTags($startTag, $endTag);
        $this->_filter->setData(array(
            'pi'     => '3.14',
        ));
        $template = "PI = {$startTag}pi{$endTag}";
        $actual = $this->_filter->filter($template);
        $expected = 'PI = 3.14';
        $this->assertSame($expected, $actual);
    }

    /**
     * @return array
     */
    public function setTagsDataProvider()
    {
        return array(
            '(brackets)' => array('(', ')'),
            '#hash#'     => array('#', '#'),
        );
    }
}
