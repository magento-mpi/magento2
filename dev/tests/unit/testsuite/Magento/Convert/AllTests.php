<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Convert
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Convert_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Magento 2 Convert');
        $suite->addTestSuite('Magento_Convert_ExcelTest');
        return $suite;
    }
}