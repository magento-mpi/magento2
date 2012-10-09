<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Performance_ScenarioTest extends PHPUnit_Framework_TestCase
{
    public function testGetTitle()
    {
        $object = new Magento_Performance_Scenario('Test title', '', array());
        $this->assertEquals('Test title', $object->getTitle());
    }

    public function testGetFile()
    {
        $object = new Magento_Performance_Scenario('', 'test/file.jmx', array());
        $this->assertEquals('test/file.jmx', $object->getFile());
    }

    public function testGetArguments()
    {
        $expectedArguments = array(
            'arg1' => 'value1',
            'arg2' => 'value2'
        );
        $object = new Magento_Performance_Scenario('', '', $expectedArguments);
        $this->assertEquals($expectedArguments, $object->getArguments());
    }
}
