<?php
/**
 * Test class for Mage_Webapi_Block_Adminhtml_Role_Edit_Tabs
 *
 * @copyright {}
 */
class Mage_Webapi_Block_Adminhtml_Role_Edit_TabsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Url|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlBuilder;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Mage_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @var Mage_Webapi_Block_Adminhtml_Role_Edit_Tabs
     */
    protected $_block;

    protected function setUp()
    {
        $this->_urlBuilder = $this->getMockBuilder('Mage_Backend_Model_Url')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_layout = $this->getMockBuilder('Mage_Core_Model_Layout')
            ->disableOriginalConstructor()
            ->setMethods(array('helper'))
            ->getMock();

        $helper = new Magento_Test_Helper_ObjectManager($this);
        $this->_block = $helper->getBlock('Mage_Webapi_Block_Adminhtml_Role_Edit_Tabs', array(
            'urlBuilder' => $this->_urlBuilder,
            'layout' => $this->_layout
        ));
    }

    /**
     * Test _construct method
     */
    public function testConstruct()
    {
        $this->assertEquals('page_tabs', $this->_block->getId());
        $this->assertEquals('edit_form', $this->_block->getDestElementId());
        $this->assertEquals('Role Information', $this->_block->getTitle());
    }

    public function testBeforeToHtml()
    {
        $this->_block->toHtml();
    }
}
