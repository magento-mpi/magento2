<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Contact\Controller;

/**
 * Contact index controller test
 */
class IndexTest extends \Magento\TestFramework\TestCase\AbstractController
{
    public function testPostAction()
    {
        $params = [
            'name' => 'customer name',
            'comment' => 'comment',
            'email' => 'user@example.com',
            'hideit' => ''
        ];
        $this->getRequest()->setPost($params);
        $transportBuilderMock = $this->getMock('Magento\Framework\Mail\Template\TransportBuilder', [], [], '', false);
        $transportBuilderMock->expects($this->once())
            ->method('setTemplateIdentifier')
            ->with($this->equalTo('contact_email_email_template'))
            ->will($this->returnSelf());
        $transportBuilderMock->expects($this->once())
            ->method('setTemplateOptions')
            ->with(
                $this->equalTo(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => 1
                    ]
                )
            )
            ->will($this->returnSelf());
        $transportBuilderMock->expects($this->once())
            ->method('setTemplateVars')
            ->will($this->returnSelf());
        $transportBuilderMock->expects($this->once())
            ->method('setFrom')
            ->with($this->equalTo('custom2'))
            ->will($this->returnSelf());
        $transportBuilderMock->expects($this->once())
            ->method('addTo')
            ->with($this->equalTo('hello@example.com'))
            ->will($this->returnSelf());
        $transportBuilderMock->expects($this->once())
            ->method('setReplyTo')
            ->with($this->equalTo($params['email']))
            ->will($this->returnSelf());

        $transportMock = $this->getMock('Magento\Framework\Mail\TransportInterface');
        $transportMock->expects($this->once())->method('sendMessage')->will($this->returnSelf());

        $transportBuilderMock->expects($this->once())
            ->method('getTransport')
            ->will($this->returnValue($transportMock));

        $this->_objectManager->addSharedInstance(
            $transportBuilderMock,
            'Magento\Framework\Mail\Template\TransportBuilder'
        );
        $this->dispatch('contact/index/post');
        $this->assertSessionMessages(
            $this->contains(
                "Thanks for contacting us with your comments and questions. We'll respond to you very soon."
            ),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
    }
}
