<?php
/**
 * Test class for Mage_Webapi_Block_Adminhtml_User_Edit
 *
 * @copyright {}
 */
class Mage_Webapi_Block_Adminhtml_User_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Mage_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @var Mage_Webapi_Block_Adminhtml_User_Edit
     */
    protected $_block;

    protected function setUp()
    {
        $this->_layout = $this->getMockBuilder('Mage_Core_Model_Layout')
            ->disableOriginalConstructor()
            ->setMethods(array('helper'))
            ->getMock();

        $helper = new Magento_Test_Helper_ObjectManager($this);
        $this->_block = $helper->getBlock('Mage_Webapi_Block_Adminhtml_User_Edit', array(
            // TODO Remove injecting of 'urlBuilder' after MAGETWO-5038 complete
            'urlBuilder' => $this->getMockBuilder('Mage_Backend_Model_Url')
                ->disableOriginalConstructor()
                ->getMock(),
            'layout' => $this->_layout
        ));
    }

    /**
     * Test getHeaderText method
     */
    public function testGetHeaderText()
    {
        $apiUser = new Varien_Object();
        $this->_block->setApiUser($apiUser);
        $this->assertEquals('New API User', $this->_block->getHeaderText());

        $apiUser->setId(1)->setApiKey('test-api');

        /** @var PHPUnit_Framework_MockObject_MockObject $coreHelper  */
        $coreHelper = $this->getMockBuilder('Mage_Core_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('escapeHtml'))
            ->getMock();
        $coreHelper->expects($this->once())
            ->method('escapeHtml')
            ->with($apiUser->getApiKey())
            ->will($this->returnArgument(0));
        $this->_layout->expects($this->once())
            ->method('helper')
            ->with('Mage_Core_Helper_Data')
            ->will($this->returnValue($coreHelper));

        $this->assertEquals("Edit API User 'test-api'", $this->_block->getHeaderText());
    }
}
