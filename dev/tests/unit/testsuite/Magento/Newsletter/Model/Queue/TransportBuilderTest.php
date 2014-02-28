<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Model\Queue;

class TransportBuilderTest extends \Magento\Mail\Template\TransportBuilderTest
{
    /**
     * @var string
     */
    protected $builderClassName = '\Magento\Newsletter\Model\Queue\TransportBuilder';

    /**
     * @var \Magento\Newsletter\Model\Queue\TransportBuilder
     */
    protected $builder;

    public function testGetTransport(
        $templateType = \Magento\App\TemplateTypesInterface::TYPE_HTML,
        $messageType = \Magento\Mail\Message::TYPE_HTML,
        $bodyText = '<h1>Html message</h1>'
    ) {
        $data = array(
            'template_subject' => 'Email Subject',
            'template_text' => $bodyText,
            'template_styles' => 'Styles',
            'template_type' => $templateType,
        );
        $vars = array(
            'reason' => 'Reason',
            'customer' => 'Customer'
        );
        $options = array(
            'area'  => 'frontend',
            'store' => 1
        );
        $template = $this->getMock('\Magento\Email\Model\Template', array(), array(), '', false);
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
            ->method('getProcessedTemplate')
            ->will($this->returnValue($bodyText));
        $template->expects($this->once())
            ->method('setData')
            ->with($this->equalTo($data))
            ->will($this->returnSelf());

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

        $this->objectManagerMock->expects($this->at(0))
            ->method('create')
            ->with(
                $this->equalTo('Magento\Mail\TransportInterface'),
                $this->equalTo(array('message' => $this->messageMock))
            )
            ->will($this->returnValue($transport));

        $this->objectManagerMock->expects($this->at(1))
            ->method('create')
            ->with($this->equalTo('Magento\Mail\Message'))
            ->will($this->returnValue($transport));

        $this->builder->setTemplateIdentifier('identifier')
            ->setTemplateVars($vars)
            ->setTemplateOptions($options)
            ->setTemplateData($data);

        $result = $this->builder->getTransport();

        $this->assertInstanceOf('Magento\Mail\TransportInterface', $result);
    }
}
