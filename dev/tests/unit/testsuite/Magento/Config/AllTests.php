<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Config_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Magento Config Library');
        $suite->addTestSuite('Magento_Config_DomTest');
        $suite->addTestSuite('Magento_Config_ThemeTest');
        $suite->addTestSuite('Magento_Config_XsdTest');
        return $suite;
    }
}
