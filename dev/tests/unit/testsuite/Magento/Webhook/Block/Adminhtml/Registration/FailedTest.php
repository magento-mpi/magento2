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

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_lastMessage = $this->getMockBuilder('Magento\Message\AbstractMessage')
            ->disableOriginalConstructor()
            ->getMock();
        $messages = $this->getMockBuilder('Magento\Message\Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $messages->expects($this->any())
            ->method('getLastAddedMessage')
            ->will($this->returnValue($this->_lastMessage));
        $messageManager = $this->getMockBuilder('Magento\Message\ManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $messageManager->expects($this->atLeastOnce())
            ->method('getMessages')
            ->will($this->returnValue($messages));

        $this->_block = $helper->getObject(
            'Magento\Webhook\Block\Adminhtml\Registration\Failed',
            array(
                'messageManager' => $messageManager
            )
        );
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
