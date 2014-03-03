<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\PageCache\Helper\Data
 */
namespace Magento\PageCache\Helper;

/**
 * Class DataTest
 *
 * @package Magento\PageCache\Controller
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    public function testMaxAgeCache()
    {
        // one year
        $age = 365 * 24 * 60 * 60;
        $this->assertEquals($age, \Magento\PageCache\Helper\Data::PRIVATE_MAX_AGE_CACHE);
    }
}
