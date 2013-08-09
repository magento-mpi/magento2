<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  static_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_CodingStandard_Tool_CodeMessDetectorTest extends PHPUnit_Framework_TestCase
{
    public function testCanRun()
    {
        $messDetector = new Magento_TestFramework_CodingStandard_Tool_CodeMessDetector('some/ruleset/file.xml', 'some/report/file.xml');
        $this->assertEquals(class_exists('PHP_PMD_TextUI_Command'), $messDetector->canRun());
    }
}
