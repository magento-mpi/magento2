<?php

/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_FunctionsTest extends Mage_PHPUnit_TestCase
{
    /**
     * @covers array_replace_recursive
     */
    public function test_array_replace_recursiveExists()
    {
        $this->assertTrue(function_exists('array_replace_recursive'));
    }

    /**
     * @covers array_replace_recursive
     *
     * @dataProvider test_array_replace_recursiveDataProvider
     */
    public function test_array_replace_recursive($arraySource, $arrayToMerge, $expected)
    {
        $result = array_replace_recursive($arraySource, $arrayToMerge);
        $this->assertNotNull($result);
        $this->assertEquals($result, $expected);
    }

    public function test_array_replace_recursiveDataProvider()
    {
        return array(
            array(array('browser' => array('default' => array('browser' => 'chrome')), 'applications' => array('magento-ce')),
                  array('browser' => array('default' => array('browser' => 'firefox'), 'firefox')),
                  array('browser' => array('default' => array('browser' => 'firefox'), 'firefox'), 'applications' => array('magento-ce'))),
        );
    }
}