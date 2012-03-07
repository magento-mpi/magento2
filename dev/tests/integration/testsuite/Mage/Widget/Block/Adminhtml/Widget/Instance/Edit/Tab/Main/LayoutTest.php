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

/**
 * @group module:Mage_Widget
 */
class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main_LayoutTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main_Layout
     */
    protected $_block;

    protected function setUp()
    {
        $layoutUtility = new Mage_Core_Utility_Layout($this);
        $pageTypesFixture = __DIR__ . '/../../../../../../../../Core/Model/Layout/_files/_page_types.xml';
        $this->_block = new Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main_Layout();
        $this->_block->setLayout($layoutUtility->getLayoutFromFixture($pageTypesFixture));
    }

    public function testGetLayoutsChooser()
    {
        $actualHtml = $this->_block->getLayoutsChooser();
        $this->assertContains('id="layout_handle"', $actualHtml);

        $expectedHtmlFile = __DIR__ . '/../../Chooser/_files/page_types_select.html';
        /** @var $expectedXml Varien_Simplexml_Element */
        $expectedXml = simplexml_load_file($expectedHtmlFile, 'Varien_Simplexml_Element');

        /** @var $actualXml Varien_Simplexml_Element */
        $actualXml = simplexml_load_string($actualHtml, 'Varien_Simplexml_Element');
        $this->assertEquals($expectedXml->innerXml(), $actualXml->innerXml());
    }
}
