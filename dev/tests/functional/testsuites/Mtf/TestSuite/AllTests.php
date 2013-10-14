<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestSuite;

class AllTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('Tests');

        $suite->addTestSuite('Magento\Catalog\Test\TestCase\ProductTest');
        return $suite;
    }
}
