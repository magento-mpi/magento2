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

/**
 * Unit test for functions
 */
class Mage_FunctionsTest extends Mage_PHPUnit_TestCase
{
    public function testArrayRecursiveMerge()
    {
        if (!function_exists('array_replace_recursive')) {
            fail('Function array_replace_recursive() doesn\'t exist');
        }

        $array = array('browser' => array('default'=> array('browser' => 'chrome')), 'applications' => array('magento-ce'));
        $array2 = array('browser' => array('default'=> array('browser' => 'firefox'), 'firefox'));
        $result = array('browser' => array('default'=> array('browser' => 'firefox'), 'firefox'), 'applications' => array('magento-ce'));

        $this->assertEquals($result, @array_replace_recursive($array, $array2));
        $this->assertNull(@array_replace_recursive('string'));
        $this->assertNull(@array_replace_recursive('string', $array));
        $this->assertNull(@array_replace_recursive($array, 'string'));
    }
}
