<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Profiler
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Profiler_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Magento Profiler');
        $suite->addTestSuite('Magento_Profiler_OutputAbstractTest');
        $suite->addTest(Magento_Profiler_Output_AllTests::suite());
        return $suite;
    }
}
