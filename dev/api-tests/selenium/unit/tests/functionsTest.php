<?php

/**
 * {license_notice}
 *
 * @category    tests
 * @package     selenium unit tests
 * @subpackage  Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
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
            array(array('a1' => array('b1' => array('c1' => 'c1Value')), 'a2' => array('b2')),
                  'string',
                  array('a1' => array('b1' => array('c1' => 'c1Value')), 'a2' => array('b2'))),
            array('string',
                  array('a1' => array('b1' => array('c1' => 'c1Value')), 'a2' => array('b2')),
                  'string'),
        );
    }
}