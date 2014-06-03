<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\CodingStandard\Tool\CodeSniffer;

class WrapperTest extends \PHPUnit_Framework_TestCase
{
    public function testSetValues()
    {
        if (!class_exists('PHP_CodeSniffer_CLI')) {
            $this->markTestSkipped('Code Sniffer is not installed');
        }
        $wrapper = new \Magento\TestFramework\CodingStandard\Tool\CodeSniffer\Wrapper();
        $expected = array('some_key' => 'some_value');
        $wrapper->setValues($expected);
        $this->assertEquals($expected, $wrapper->getCommandLineValues());
    }
}
