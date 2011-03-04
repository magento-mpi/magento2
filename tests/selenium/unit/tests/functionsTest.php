<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium unit tests
 * @subpackage  Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

        $this->assertEquals($result, array_replace_recursive($array, $array2));
        $this->assertNull(array_replace_recursive('string'));
        $this->assertNull(array_replace_recursive('string', $array));
        $this->assertEquals($array, array_replace_recursive($array, 'string'));
    }
}