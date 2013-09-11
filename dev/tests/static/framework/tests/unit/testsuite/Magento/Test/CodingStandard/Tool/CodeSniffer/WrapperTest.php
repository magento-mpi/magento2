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

class Magento_Test_CodingStandard_Tool_CodeSniffer_WrapperTest extends PHPUnit_Framework_TestCase
{
    public function testSetValues()
    {
        if (!class_exists('PHP_CodeSniffer_CLI')) {
            $this->markTestSkipped('Code Sniffer is not installed');
        }
        $wrapper = new Magento_TestFramework_CodingStandard_Tool_CodeSniffer_Wrapper();
        $expected = array('some_key' => 'some_value');
        $wrapper->setValues($expected);
        $this->assertEquals($expected, $wrapper->getCommandLineValues());
    }
}
