<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Model\Rma\Status;

/**
 * Class HistoryTest
 * @package Magento\Rma\Model\Rma\Status
 */
class HistoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var History
     */
    protected $history;

    /**
     * @var \Magento\Rma\Model\Config | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaConfig;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Rma\Helper\Data | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaHelper;

    /**
     * @var \Magento\Framework\Model\Resource\AbstractResource | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $date;

    /**
     * @var \Magento\Framework\Event\Manager | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManager;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->eventManager = $this->getMock('Magento\Framework\Event\Manager', [], [], '', false);

        $context = $this->getMock('Magento\Framework\Model\Context', [], [], '', false);
        $context->expects($this->once())->method('getEventDispatcher')->will($this->returnValue($this->eventManager));

        $this->rmaConfig = $this->getMock('Magento\Rma\Model\Config', [], [], '', false);
        $this->inlineTranslation = $this->getMock(
            'Magento\Framework\Translate\Inline\StateInterface',
            [],
            [],
            '',
            false
        );
        $this->transportBuilder = $this->getMock('Magento\Framework\Mail\Template\TransportBuilder', [], [], '', false);
        $this->rmaHelper = $this->getMock('Magento\Rma\Helper\Data', [], [], '', false);
        $this->resource = $this->getMock('Magento\Rma\Model\Resource\Rma\Status\History', [], [], '', false);
        $this->date = $this->getMock('Magento\Framework\Stdlib\DateTime\DateTime', [], [], '', false);

        $this->history = $objectManagerHelper->getObject(
            'Magento\Rma\Model\Rma\Status\History',
            [
                'rmaConfig' => $this->rmaConfig,
                'transportBuilder' => $this->transportBuilder,
                'inlineTranslation' => $this->inlineTranslation,
                'rmaHelper' => $this->rmaHelper,
                'resource' => $this->resource,
                'date' => $this->date,
                'context' => $context
            ]
        );
    }

    public function testSaveComment()
    {
        $comment = 'comment';
        $visible = true;
        $isAdmin = true;
        $id = 1;
        $status = 'status';
        $emailSent = true;
        $date = 'today';

        $rma = $this->getMock(
            'Magento\Rma\Model\Rma',
            ['__wakeup', 'getId', 'getStatus'],
            [],
            '',
            false
        );
        $rma->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($id));
        $rma->expects($this->once())
            ->method('getStatus')
            ->will($this->returnValue($status));

        $this->date->expects($this->once())
            ->method('gmtDate')
            ->will($this->returnValue($date));

        $this->resource->expects($this->once())
            ->method('addCommitCallback')
            ->will($this->returnSelf());
        $this->resource->expects($this->once())
            ->method('save')
            ->with($this->history);

        $this->history->setRma($rma);
        $this->history->setEmailSent($emailSent);

        $this->history->saveComment($comment, $visible, $isAdmin);

        $this->assertEquals($comment, $this->history->getComment());
        $this->assertEquals($visible, $this->history->getIsVisibleOnFront());
        $this->assertEquals($isAdmin, $this->history->getIsAdmin());
        $this->assertEquals($emailSent, $this->history->getIsCustomerNotified());
        $this->assertEquals($date, $this->history->getCreatedAt());
        $this->assertEquals($status, $this->history->getStatus());
    }

    public function testSendNewRmaEmail()
    {
        $storeId = 5;
        $order = $this->getMock('Magento\Sales\Model\Order', [], [], '', false);

        $rma = $this->getMock(
            'Magento\Rma\Model\Rma',
            ['getStoreId', 'getOrder', '__wakeup', 'getItemsForDisplay'],
            [],
            '',
            false
        );
        $rma->expects($this->once())
            ->method('getStoreId')
            ->will($this->returnValue($storeId));
        $rma->expects($this->once())
            ->method('getOrder')
            ->will($this->returnValue($order));

        $this->rmaConfig->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));

        $this->transportBuilder->expects($this->once())
            ->method('setTemplateIdentifier')
            ->will($this->returnSelf());
        $this->transportBuilder->expects($this->once())
            ->method('setTemplateOptions')
            ->will($this->returnSelf());
        $this->transportBuilder->expects($this->once())
            ->method('setTemplateVars')
            ->will($this->returnSelf());
        $this->transportBuilder->expects($this->once())
            ->method('setFrom')
            ->will($this->returnSelf());
        $this->transportBuilder->expects($this->once())
            ->method('addTo')
            ->will($this->returnSelf());
        $this->transportBuilder->expects($this->once())
            ->method('addBcc')
            ->will($this->returnSelf());

        $transport = $this->getMock('Magento\Framework\Mail\Transport', [], [], '', false);
        $transport->expects($this->once())
            ->method('sendMessage');

        $this->transportBuilder->expects($this->once())
            ->method('getTransport')
            ->will($this->returnValue($transport));

        $this->history->setRma($rma);
        $this->assertNull($this->history->getEmailSent());
        $this->history->sendNewRmaEmail();
        $this->assertTrue($this->history->getEmailSent());
    }

    public function testSendAuthorizeEmail()
    {
        $storeId = 5;
        $order = $this->getMock('Magento\Sales\Model\Order', [], [], '', false);

        $rma = $this->getMock(
            'Magento\Rma\Model\Rma',
            ['getStoreId', 'getOrder', '__wakeup', 'getItemsForDisplay'],
            [],
            '',
            false
        );
        $rma->expects($this->once())
            ->method('getStoreId')
            ->will($this->returnValue($storeId));
        $rma->expects($this->once())
            ->method('getOrder')
            ->will($this->returnValue($order));

        $this->rmaConfig->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));

        $this->transportBuilder->expects($this->once())
            ->method('setTemplateIdentifier')
            ->will($this->returnSelf());
        $this->transportBuilder->expects($this->once())
            ->method('setTemplateOptions')
            ->will($this->returnSelf());
        $this->transportBuilder->expects($this->once())
            ->method('setTemplateVars')
            ->will($this->returnSelf());
        $this->transportBuilder->expects($this->once())
            ->method('setFrom')
            ->will($this->returnSelf());
        $this->transportBuilder->expects($this->once())
            ->method('addTo')
            ->will($this->returnSelf());
        $this->transportBuilder->expects($this->once())
            ->method('addBcc')
            ->will($this->returnSelf());

        $transport = $this->getMock('Magento\Framework\Mail\Transport', [], [], '', false);
        $transport->expects($this->once())
            ->method('sendMessage');

        $this->transportBuilder->expects($this->once())
            ->method('getTransport')
            ->will($this->returnValue($transport));

        $rma->setIsSendAuthEmail(true);
        $this->history->setRma($rma);
        $this->assertNull($this->history->getEmailSent());
        $this->history->sendAuthorizeEmail();
        $this->assertTrue($this->history->getEmailSent());
    }
}
