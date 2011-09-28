<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Magento');
        $suite->addTest(Magento_AllTests::suite());
        return $suite;
    }
}
