<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  static_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class CodingStandard_Tool_CodeSniffer_WrapperTest extends PHPUnit_Framework_TestCase
{
    public function testSetValues()
    {
        $wrapper = new CodingStandard_Tool_CodeSniffer_Wrapper();
        $expected = array('some_key' => 'some_value');
        $wrapper->setValues($expected);
        $this->assertEquals($expected, $wrapper->getCommandLineValues());
    }
}
