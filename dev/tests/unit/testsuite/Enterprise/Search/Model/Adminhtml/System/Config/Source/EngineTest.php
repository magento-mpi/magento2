<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Search
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Search_Model_Adminhtml_System_Config_Source_EngineTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Search_Model_Adminhtml_System_Config_Source_Engine
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model= new Enterprise_Search_Model_Adminhtml_System_Config_Source_Engine();
    }

    /**
     * Check if Enterprise_Search_Model_Adminhtml_System_Config_Source_Engine has method toOptionArray
     */
    public function testToOptionArrayExistence()
    {
        $this->assertTrue(method_exists($this->_model, 'toOptionArray'), 'Required method toOptionArray not exists');
    }

    /**
     * Check output format
     * @depends testToOptionArrayExistence
     */
    public function testToOptionArrayFormat()
    {
        $options = $this->_model->toOptionArray();
        $this->assertNotEmpty($options);
        $labels = array('MySql Fulltext', 'Solr');
        foreach ($options as $option) {
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('value', $option);
            $this->assertContains((string)$option['label'], $labels);
            $this->assertTrue(class_exists($option['value']));
        }
    }
}
