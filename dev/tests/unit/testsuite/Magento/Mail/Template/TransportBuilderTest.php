<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Mail\Template;

class TransportBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $builderClassName = '\Magento\Mail\Template\TransportBuilder';

    /**
     * @var \Magento\Mail\Template\TransportBuilder
     */
    protected $builder;

    /**
     * @var \Magento\Mail\Template\FactoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $templateFactoryMock;

    /**
     * @var \Magento\Mail\Message | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageMock;

    /**
     * @var \Magento\ObjectManager | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \Magento\Mail\Template\SenderResolverInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $senderResolverMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_mailTransportFactoryMock;

    public function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->templateFactoryMock = $this->getMock('Magento\Mail\Template\FactoryInterface');
        $this->messageMock = $this->getMock('Magento\Mail\Message');
        $this->objectManagerMock = $this->getMock('Magento\ObjectManager');
        $this->senderResolverMock = $this->getMock('Magento\Mail\Template\SenderResolverInterface');
        $this->_mailTransportFactoryMock = $this->getMockBuilder(
            'Magento\Mail\TransportInterfaceFactory'
        )->disableOriginalConstructor()->setMethods(['create'])->getMock();
        $this->builder = $helper->getObject(
            $this->builderClassName,
            array(
                'templateFactory' => $this->templateFactoryMock,
                'message' => $this->messageMock,
                'objectManager' => $this->objectManagerMock,
                'senderResolver' => $this->senderResolverMock,
                'mailTransportFactory' => $this->_mailTransportFactoryMock
            )
        );
    }

    /**
     * @dataProvider getTransportDataProvider
     * @param int $templateType
     * @param string $messageType
     * @param string $bodyText
     */
    public function testGetTransport($templateType, $messageType, $bodyText)
    {
        $vars = array('reason' => 'Reason', 'customer' => 'Customer');
        $options = array('area' => 'frontend', 'store' => 1);
        $template = $this->getMock('\Magento\Mail\TemplateInterface');
        $template->expects($this->once())->method('setVars')->with($this->equalTo($vars))->will($this->returnSelf());
        $template->expects(
            $this->once()
        )->method(
            'setOptions'
        )->with(
            $this->equalTo($options)
        )->will(
            $this->returnSelf()
        );
        $template->expects($this->once())->method('getSubject')->will($this->returnValue('Email Subject'));
        $template->expects($this->once())->method('getType')->will($this->returnValue($templateType));
        $template->expects($this->once())->method('processTemplate')->will($this->returnValue($bodyText));

        $this->templateFactoryMock->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            $this->equalTo('identifier')
        )->will(
            $this->returnValue($template)
        );

        $this->messageMock->expects(
            $this->once()
        )->method(
            'setSubject'
        )->with(
            $this->equalTo('Email Subject')
        )->will(
            $this->returnSelf()
        );
        $this->messageMock->expects(
            $this->once()
        )->method(
            'setMessageType'
        )->with(
            $this->equalTo($messageType)
        )->will(
            $this->returnSelf()
        );
        $this->messageMock->expects(
            $this->once()
        )->method(
            'setBody'
        )->with(
            $this->equalTo($bodyText)
        )->will(
            $this->returnSelf()
        );

        $transport = $this->getMock('\Magento\Mail\TransportInterface');

        $this->_mailTransportFactoryMock->expects(
            $this->at(0)
        )->method(
            'create'
        )->with(
            $this->equalTo(array('message' => $this->messageMock))
        )->will(
            $this->returnValue($transport)
        );

        $this->objectManagerMock->expects(
            $this->at(0)
        )->method(
            'create'
        )->with(
            $this->equalTo('Magento\Mail\Message')
        )->will(
            $this->returnValue($transport)
        );

        $this->builder->setTemplateIdentifier('identifier')->setTemplateVars($vars)->setTemplateOptions($options);

        $result = $this->builder->getTransport();

        $this->assertInstanceOf('Magento\Mail\TransportInterface', $result);
    }

    public function getTransportDataProvider()
    {
        return array(
            array(
                \Magento\Framework\App\TemplateTypesInterface::TYPE_TEXT,
                \Magento\Mail\Message::TYPE_TEXT,
                'Plain text'
            ),
            array(
                \Magento\Framework\App\TemplateTypesInterface::TYPE_HTML,
                \Magento\Mail\Message::TYPE_HTML,
                '<h1>Html message</h1>'
            )
        );
    }

    public function testSetFrom()
    {
        $sender = array('email' => 'from@example.com', 'name' => 'name');
        $this->senderResolverMock->expects(
            $this->once()
        )->method(
            'resolve'
        )->with(
            $sender
        )->will(
            $this->returnValue($sender)
        );
        $this->messageMock->expects(
            $this->once()
        )->method(
            'setFrom'
        )->with(
            'from@example.com',
            'name'
        )->will(
            $this->returnSelf()
        );

        $this->builder->setFrom($sender);
    }

    public function testSetCc()
    {
        $this->messageMock->expects($this->once())->method('addCc')->with('cc@example.com')->will($this->returnSelf());

        $this->builder->addCc('cc@example.com');
    }

    /**
     * @covers \Magento\Mail\Template\TransportBuilder::addTo
     */
    public function testAddTo()
    {
        $this->messageMock->expects($this->once())
            ->method('addTo')
            ->with('to@example.com', 'recipient')
            ->will($this->returnSelf());

        $this->builder->addTo('to@example.com', 'recipient');
    }

    /**
     * @covers \Magento\Mail\Template\TransportBuilder::addBcc
     */
    public function testAddBcc()
    {
        $this->messageMock->expects($this->once())
            ->method('addBcc')
            ->with('bcc@example.com')
            ->will($this->returnSelf());

        $this->builder->addBcc('bcc@example.com');
    }

    /**
     * @covers \Magento\Mail\Template\TransportBuilder::setReplyTo
     */
    public function testSetReplyTo()
    {
        $this->messageMock->expects($this->once())
            ->method('setReplyTo')
            ->with('replyTo@example.com', 'replyName')
            ->will($this->returnSelf());

        $this->builder->setReplyTo('replyTo@example.com', 'replyName');
    }
}
