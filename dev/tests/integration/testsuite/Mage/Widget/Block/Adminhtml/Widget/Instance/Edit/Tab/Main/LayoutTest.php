<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main_LayoutTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main_Layout
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = new Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main_Layout(array(
            'widget_instance' => new Mage_Widget_Model_Widget_Instance()
        ));
        $this->_block->setLayout(Mage::app()->getLayout());
    }

    public function testGetLayoutsChooser()
    {
        $actualHtml = $this->_block->getLayoutsChooser();
        $this->assertStringStartsWith('<select ', $actualHtml);
        $this->assertStringEndsWith('</select>', $actualHtml);
        $this->assertContains('id="layout_handle"', $actualHtml);
        $optionCount = substr_count($actualHtml, '<option ');
        $this->assertGreaterThan(1, $optionCount, 'HTML select tag must provide options to choose from.');
        $this->assertEquals($optionCount, substr_count($actualHtml, '</option>'));
    }
}
