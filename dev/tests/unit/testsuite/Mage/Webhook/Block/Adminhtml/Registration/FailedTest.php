<?php
/**
 * Mage_Webhook_Block_Adminhtml_Registration_Failed
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Block_Adminhtml_Registration_FailedTest extends PHPUnit_Framework_TestCase
{
    /** @var  Mage_Webhook_Block_Adminhtml_Registration_Failed */
    private $_block;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_lastMessage;

    public function setUp()
    {
        $urlBuilder = $this->getMock('Mage_Core_Model_Url', array('getUrl'), array(), '', false);

        $context = $this->getMockBuilder('Mage_Backend_Block_Template_Context')
            ->disableOriginalConstructor()
            ->getMock();
        $context->expects($this->once())
            ->method('getUrlBuilder')
            ->will($this->returnValue($urlBuilder));

        $this->_lastMessage = $this->getMockBuilder('Mage_Core_Model_Message_Abstract')
            ->disableOriginalConstructor()
            ->getMock();
        $messages = $this->getMockBuilder('Mage_Core_Model_Message_Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $messages->expects($this->any())
            ->method('getLastAddedMessage')
            ->will($this->returnValue($this->_lastMessage));
        $session = $this->getMockBuilder('Mage_Backend_Model_Session')
            ->disableOriginalConstructor()
            ->getMock();
        $session->expects($this->once())
            ->method('getMessages')
            ->will($this->returnValue($messages));
        $this->_block = new Mage_Webhook_Block_Adminhtml_Registration_Failed($session, $context);
    }

    public function testGetSessionError()
    {
        $errorMessage = 'Some error message';
        $this->_lastMessage->expects($this->once())
            ->method('toString')
            ->will($this->returnValue($errorMessage));

        $this->assertEquals($errorMessage, $this->_block->getSessionError());
    }

}