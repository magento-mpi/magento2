<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * @magentoAppArea adminhtml
 */
class Magento_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main_LayoutTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main_Layout
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();

        $this->_block = Mage::app()->getLayout()->createBlock(
            'Magento_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main_Layout',
            '',
            array('data' => array('widget_instance' => Mage::getModel('Magento_Widget_Model_Widget_Instance')))
        );
        $this->_block->setLayout(Mage::app()->getLayout());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetLayoutsChooser()
    {
        Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface')
            ->setArea(Magento_Core_Model_App_Area::AREA_FRONTEND)
            ->setDefaultDesignTheme();

        $actualHtml = $this->_block->getLayoutsChooser();
        $this->assertStringStartsWith('<select ', $actualHtml);
        $this->assertStringEndsWith('</select>', $actualHtml);
        $this->assertContains('id="layout_handle"', $actualHtml);
        $optionCount = substr_count($actualHtml, '<option ');
        $this->assertGreaterThan(1, $optionCount, 'HTML select tag must provide options to choose from.');
        $this->assertEquals($optionCount, substr_count($actualHtml, '</option>'));
    }
}
