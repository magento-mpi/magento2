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

class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_LayoutTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Layout|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_block;

    protected function setUp()
    {
        $layoutUtility = new Mage_Core_Utility_Layout($this);
        $pageTypesFixture = __DIR__ . '/_files/_page_types_with_containers.xml';
        $this->_block = $this->getMock(
            'Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Layout',
            array('_getLayoutUpdate'),
            array(array(
                'name'  => 'page_type',
                'id'    => 'page_types_select',
                'class' => 'page-types-select',
                'title' => 'Page Types Select',
            ))
        );
        $this->_block
            ->expects($this->any())
            ->method('_getLayoutUpdate')
            ->will($this->returnValue($layoutUtility->getLayoutUpdateFromFixture($pageTypesFixture)))
        ;
    }

    public function testToHtml()
    {
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/_files/page_types_select.html', $this->_block->toHtml());
    }
}
