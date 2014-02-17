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

    public function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->templateFactoryMock = $this->getMock('\Magento\Mail\Template\FactoryInterface');
        $this->messageMock = $this->getMock('\Magento\Mail\Message');
        $this->objectManagerMock = $this->getMock('\Magento\ObjectManager');
        $this->senderResolverMock = $this->getMock('\Magento\Mail\Template\SenderResolverInterface');

        $this->builder = $helper->getObject('\Magento\Mail\Template\TransportBuilder', array(
            'templateFactory' => $this->templateFactoryMock,
            'message' => $this->messageMock,
            'objectManager' => $this->objectManagerMock,
            'senderResolver' => $this->senderResolverMock
        ));
    }

    /**
     * @dataProvider getTransportDataProvider
     * @param int $templateType
     * @param string $messageType
     * @param string $bodyText
     */
    public function testGetTransport($templateType, $messageType, $bodyText)
    {
        $vars = array(
            'reason' => 'Reason',
            'customer' => 'Customer'
        );
        $options = array(
            'area'  => 'frontend',
            'store' => 1
        );
        $template = $this->getMock('\Magento\Mail\TemplateInterface');
        $template->expects($this->once())
            ->method('setVars')
            ->with($this->equalTo($vars))
            ->will($this->returnSelf());
        $template->expects($this->once())
            ->method('setOptions')
            ->with($this->equalTo($options))
            ->will($this->returnSelf());
        $template->expects($this->once())
            ->method('getSubject')
            ->will($this->returnValue('Email Subject'));
        $template->expects($this->once())
            ->method('getType')
            ->will($this->returnValue($templateType));
        $template->expects($this->once())
            ->method('processTemplate')
            ->will($this->returnValue($bodyText));

        $this->templateFactoryMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('identifier'))
            ->will($this->returnValue($template));

        $this->messageMock->expects($this->once())
            ->method('setSubject')
            ->with($this->equalTo('Email Subject'))
            ->will($this->returnSelf());
        $this->messageMock->expects($this->once())
            ->method('setMessageType')
            ->with($this->equalTo($messageType))
            ->will($this->returnSelf());
        $this->messageMock->expects($this->once())
            ->method('setBody')
            ->with($this->equalTo($bodyText))
            ->will($this->returnSelf());

        $transport = $this->getMock('\Magento\Mail\TransportInterface');

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('Magento\Mail\TransportInterface'),
                $this->equalTo(array('message' => $this->messageMock))
            )
            ->will($this->returnValue($transport));

        $this->builder->setTemplateIdentifier('identifier')
            ->setTemplateVars($vars)
            ->setTemplateOptions($options);

        $result = $this->builder->getTransport();

        $this->assertInstanceOf('\Magento\Mail\TransportInterface', $result);
    }

    public function getTransportDataProvider()
    {
        return array(
            array(
                \Magento\App\TemplateTypesInterface::TYPE_TEXT,
                \Magento\Mail\Message::TYPE_TEXT,
                'Plain text'
            ),
            array(
                \Magento\App\TemplateTypesInterface::TYPE_HTML,
                \Magento\Mail\Message::TYPE_HTML,
                '<h1>Html message</h1>'
            )
        );
    }

    public function testSetFrom()
    {
        $sender = array('email' => 'from@example.com', 'name' => 'name');
        $this->senderResolverMock->expects($this->once())
            ->method('resolve')
            ->with($sender)
            ->will($this->returnValue($sender));
        $this->messageMock->expects($this->once())
            ->method('setFrom')
            ->with('from@example.com', 'name')
            ->will($this->returnSelf());

        $this->builder->setFrom($sender);
    }

    public function testSetCc()
    {
        $this->messageMock->expects($this->once())
            ->method('addCc')
            ->with('cc@example.com')
            ->will($this->returnSelf());

        $this->builder->addCc('cc@example.com');
    }
}
