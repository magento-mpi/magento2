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

class Magento_Profiler_Output_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Magento Profiler');
        $suite->addTestSuite('Magento_Profiler_Output_HtmlTest');
        $suite->addTestSuite('Magento_Profiler_Output_CsvfileTest');
        $suite->addTestSuite('Magento_Profiler_Output_FirebugTest');
        return $suite;
    }
}
