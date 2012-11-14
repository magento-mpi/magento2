<?php
/**
 * Test class for Mage_Webapi_Block_Adminhtml_RoleTest
 *
 * @copyright {}
 */
class Mage_Webapi_Block_Adminhtml_RoleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Url|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlBuilder;

    /**
     * @var Mage_Webapi_Block_Adminhtml_Role
     */
    protected $_block;

    protected function setUp()
    {
        $this->_urlBuilder = $this->getMockBuilder('Mage_Backend_Model_Url')
            ->disableOriginalConstructor()
            ->getMock();

        $helper = new Magento_Test_Helper_ObjectManager($this);
        $this->_block = $helper->getBlock('Mage_Webapi_Block_Adminhtml_Role', array(
            'urlBuilder' => $this->_urlBuilder
        ));
    }

    /**
     * Test getCreateUrl method
     */
    public function testGetCreateUrl()
    {
        $expectedUrl = 'create_url';
        $this->_urlBuilder
            ->expects($this->once())
            ->method('getUrl')
            ->with('*/*/edit', array())
            ->will($this->returnValue($expectedUrl));

        $this->assertEquals($expectedUrl, $this->_block->getCreateUrl());
    }
}
