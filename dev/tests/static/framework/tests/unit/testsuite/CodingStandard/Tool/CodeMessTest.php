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

class CodingStandard_Tool_CodeMessTest extends PHPUnit_Framework_TestCase
{
    public function testCanRun()
    {
        $messDetector = new CodingStandard_Tool_CodeMess('some/ruleset/file.xml', 'some/report/file.xml');
        $this->assertEquals(class_exists('PHP_PMD_TextUI_Command'), $messDetector->canRun());
    }
}
