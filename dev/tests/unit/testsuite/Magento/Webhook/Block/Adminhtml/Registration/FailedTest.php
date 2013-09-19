<?php
/**
 * \Magento\Webhook\Block\Adminhtml\Registration\Failed
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Block\Adminhtml\Registration;

class FailedTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \Magento\Webhook\Block\Adminhtml\Registration\Failed */
    private $_block;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $_lastMessage;

    public function setUp()
    {
        $urlBuilder = $this->getMock('Magento\Core\Model\Url', array('getUrl'), array(), '', false);

        /** @var  $coreData \Magento\Core\Helper\Data */
        $coreData = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);

        $context = $this->getMockBuilder('Magento\Backend\Block\Template\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $context->expects($this->once())
            ->method('getUrlBuilder')
            ->will($this->returnValue($urlBuilder));

        $this->_lastMessage = $this->getMockBuilder('Magento\Core\Model\Message\AbstractMessage')
            ->disableOriginalConstructor()
            ->getMock();
        $messages = $this->getMockBuilder('Magento\Core\Model\Message\Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $messages->expects($this->any())
            ->method('getLastAddedMessage')
            ->will($this->returnValue($this->_lastMessage));
        $session = $this->getMockBuilder('Magento\Backend\Model\Session')
            ->disableOriginalConstructor()
            ->getMock();
        $session->expects($this->once())
            ->method('getMessages')
            ->will($this->returnValue($messages));
        $this->_block = new \Magento\Webhook\Block\Adminhtml\Registration\Failed($coreData, $session, $context);
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
