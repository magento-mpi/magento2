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
class Magento_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_LayoutTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Layout|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();

        $layoutUtility = new Magento_Core_Utility_Layout($this);
        $pageTypesFixture = __DIR__ . '/_files/_page_types_with_containers.xml';
        $args = array(
            'context'            => Mage::getSingleton('Magento_Core_Block_Template_Context'),
            'layoutMergeFactory' => Mage::getSingleton('Magento_Core_Model_Layout_MergeFactory'),
            'themesFactory'      => Mage::getSingleton('Magento_Core_Model_Resource_Theme_CollectionFactory'),
            'data' => array(
                'name'  => 'page_type',
                'id'    => 'page_types_select',
                'class' => 'page-types-select',
                'title' => 'Page Types Select',
            )
        );
        $this->_block = $this->getMock(
            'Magento_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Layout',
            array('_getLayoutMerge'), $args
        );
        $this->_block
            ->expects($this->any())
            ->method('_getLayoutMerge')
            ->will($this->returnValue($layoutUtility->getLayoutUpdateFromFixture(
            $pageTypesFixture,
            $layoutUtility->getLayoutDependencies()
        )))
        ;
    }

    public function testToHtml()
    {
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/_files/page_types_select.html', $this->_block->toHtml());
    }
}
