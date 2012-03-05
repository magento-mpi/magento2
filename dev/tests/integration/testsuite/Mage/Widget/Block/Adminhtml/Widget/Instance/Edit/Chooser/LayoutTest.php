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
class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_LayoutTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Layout
     */
    protected $_block;

    protected function setUp()
    {
        $layoutUtility = new Mage_Core_Utility_Layout($this);
        $pageTypesFixture = __DIR__ . '/../../../../../../../Core/Model/Layout/_files/_page_types.xml';
        $this->_block = new Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Layout(array(
            'name'  => 'page_type',
            'id'    => 'page_types_select',
            'class' => 'page-types-select',
            'title' => 'Page Types Select',
        ));
        $this->_block->setLayout($layoutUtility->getLayoutFromFixture($pageTypesFixture));
    }

    public function testToHtml()
    {
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/_files/page_types_select.html', $this->_block->toHtml());
    }
}
