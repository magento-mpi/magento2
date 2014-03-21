<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Mail;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject
     */
    protected $_messageMock;

    public function setUp()
    {
        $this->_messageMock = $this->getMock(
            '\Magento\Mail\Message',
            array('getBodyText', 'getBodyHtml', 'setBodyText', 'setBodyHtml')
        );
    }

    /**
     * @param string $messageType
     * @param string $method
     *
     * @covers \Magento\Mail\Message::setBody
     * @covers \Magento\Mail\Message::setMessageType
     * @dataProvider setBodyDataProvider
     */
    public function testSetBody($messageType, $method)
    {
        $this->_messageMock->setMessageType($messageType);

        $this->_messageMock->expects($this->once())
            ->method($method)
            ->with('body');

        $this->_messageMock->setBody('body');
    }

    /**
     * @return array
     */
    public function setBodyDataProvider()
    {
        return array(
            array(
                'messageType' => 'text/plain',
                'method' => 'setBodyText'
            ),
            array(
                'messageType' => 'text/html',
                'method' => 'setBodyHtml'
            )
        );
    }

    /**
     * @param string $messageType
     * @param string $method
     *
     * @covers \Magento\Mail\Message::getBody
     * @covers \Magento\Mail\Message::setMessageType
     * @dataProvider getBodyDataProvider
     */
    public function testGetBody($messageType, $method)
    {
        $this->_messageMock->setMessageType($messageType);

        $this->_messageMock->expects($this->once())
            ->method($method);

        $this->_messageMock->getBody('body');
    }

    /**
     * @return array
     */
    public function getBodyDataProvider()
    {
        return array(
            array(
                'messageType' => 'text/plain',
                'method' => 'getBodyText'
            ),
            array(
                'messageType' => 'text/html',
                'method' => 'getBodyHtml'
            )
        );
    }
}
