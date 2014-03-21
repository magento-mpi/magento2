<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Mail;

class TransportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject
     */
    protected $_messageMock;

    /**
     * @var \Magento\Mail\Transport
     */
    protected $_transport;

    public function setUp()
    {
        $this->_messageMock = $this->getMock('\Magento\Mail\Message', array(), array(), '', false);
        $this->_transport = new \Magento\Mail\Transport($this->_messageMock);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The message should be an instance of \Zend_Mail
     */
    public function testTransportWithIncorrectMessageObject()
    {
        $this->_messageMock = $this->getMock('\Magento\Mail\MessageInterface');
        $this->_transport = new \Magento\Mail\Transport($this->_messageMock);
    }

    /**
     * @covers \Magento\Mail\Transport::sendMessage
     * @expectedException \Magento\Mail\Exception
     * @expectedExceptionMessage No body specified
     */
    public function testSendMessageBrokenMessage()
    {
        $this->_messageMock->expects($this->any())
            ->method('getParts')
            ->will($this->returnValue(array('a','b')));

        $this->_transport->sendMessage();
    }
}
