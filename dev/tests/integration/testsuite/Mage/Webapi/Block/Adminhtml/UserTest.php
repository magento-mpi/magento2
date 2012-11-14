<?php
/**
 * Test class for Mage_Webapi_Block_Adminhtml_User
 *
 * @copyright {}
 */
class Mage_Webapi_Block_Adminhtml_UserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Model_Event_Manager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManager;

    /**
     * @var Mage_Core_Model_BlockFactory
     */
    protected $_blockFactory;

    /**
     * @var Mage_Webapi_Block_Adminhtml_User
     */
    protected $_block;

    protected function setUp()
    {
        $this->_objectManager = Mage::getObjectManager();

        $this->_eventManager = $this->getMockBuilder('Mage_Core_Model_Event_Manager')
            ->disableOriginalConstructor()
            ->setMethods(array('dispatch'))
            ->getMock();

        $this->_blockFactory = $this->_objectManager->get('Mage_Core_Model_BlockFactory');
        $this->_block = $this->_blockFactory->createBlock('Mage_Webapi_Block_Adminhtml_User', array(
            'eventManager' => $this->_eventManager,
        ));
    }

    protected function tearDown()
    {
        $this->_objectManager->removeSharedInstance('Mage_Core_Model_Layout');
        unset($this->_objectManager, $this->_eventManager, $this->_blockFactory, $this->_block);
    }

    /**
     * Test event dispatch in Mage_Webapi_Block_Adminhtml_User::_toHtml()
     */
    public function testToHtml()
    {
        // TODO Move to unit tests after MAGETWO-4015 complete
        $this->_eventManager->expects($this->at(0))
            ->method('dispatch')
            ->with('core_block_abstract_to_html_before', array('block' => $this->_block));
        $this->_eventManager->expects($this->at(1))
            ->method('dispatch')
            ->with('webapi_user_html_before', array('block' => $this->_block));
        $this->_block->toHtml();
    }
}
