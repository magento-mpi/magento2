<?php
/**
 * Magento_Webhook_Block_Adminhtml_Registration_Failed
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Block_Adminhtml_Registration_FailedTest extends PHPUnit_Framework_TestCase
{
    /** @var  Magento_Webhook_Block_Adminhtml_Registration_Failed */
    private $_block;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_lastMessage;

    public function setUp()
    {
        $urlBuilder = $this->getMock('Magento_Core_Model_Url', array('getUrl'), array(), '', false);

        $context = $this->getMockBuilder('Magento_Backend_Block_Template_Context')
            ->disableOriginalConstructor()
            ->getMock();
        $context->expects($this->once())
            ->method('getUrlBuilder')
            ->will($this->returnValue($urlBuilder));

        $this->_lastMessage = $this->getMockBuilder('Magento_Core_Model_Message_Abstract')
            ->disableOriginalConstructor()
            ->getMock();
        $messages = $this->getMockBuilder('Magento_Core_Model_Message_Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $messages->expects($this->any())
            ->method('getLastAddedMessage')
            ->will($this->returnValue($this->_lastMessage));
        $session = $this->getMockBuilder('Magento_Backend_Model_Session')
            ->disableOriginalConstructor()
            ->getMock();
        $session->expects($this->once())
            ->method('getMessages')
            ->will($this->returnValue($messages));
        $this->_block = new Magento_Webhook_Block_Adminhtml_Registration_Failed($session, $context);
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
