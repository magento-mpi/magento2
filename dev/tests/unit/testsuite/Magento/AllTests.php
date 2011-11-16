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

class Magento_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Magento Library');
        $suite->addTestSuite('Magento_AutoloadTest');
        $suite->addTestSuite('Magento_CryptTest');
        $suite->addTestSuite('Magento_ProfilerTest');
        $suite->addTest(Magento_Config_AllTests::suite());
        $suite->addTest(Magento_Profiler_AllTests::suite());
        return $suite;
    }
}
