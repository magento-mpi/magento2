<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Model\Rma\Status;

use Magento\Rma\Model\Rma\Source\Status;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

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
     * @var \Magento\Framework\Stdlib\DateTime | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dateTimeDateTime;

    /**
     * @var \Magento\Framework\Event\Manager | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManager;

    /**
     * @var TimezoneInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeDate;

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
        $this->dateTime = $this->getMock('Magento\Framework\Stdlib\DateTime', [], [], '', false);
        $this->dateTimeDateTime = $this->getMock('Magento\Framework\Stdlib\DateTime\DateTime', [], [], '', false);
        $this->localeDate = $this->getMock('Magento\Framework\Stdlib\DateTime\Timezone', [], [], '', false);

        $this->history = $objectManagerHelper->getObject(
            'Magento\Rma\Model\Rma\Status\History',
            [
                'rmaConfig' => $this->rmaConfig,
                'transportBuilder' => $this->transportBuilder,
                'inlineTranslation' => $this->inlineTranslation,
                'rmaHelper' => $this->rmaHelper,
                'resource' => $this->resource,
                'dateTime' => $this->dateTime,
                'dateTimeDateTime' => $this->dateTimeDateTime,
                'localeDate' => $this->localeDate,
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

        $this->dateTimeDateTime->expects($this->once())
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

    public function testGetCreatedAtDate()
    {
        $date = '42';
        $timestamp = 43;
        $expected = new \Magento\Framework\Stdlib\DateTime\Date();
        $this->dateTime->expects($this->once())
            ->method('toTimestamp')
            ->with($date)
            ->will($this->returnValue($timestamp));
        $this->localeDate->expects($this->once())
            ->method('date')
            ->with($timestamp, null, null, true)
            ->will($this->returnValue($expected));

        $this->history->setCreatedAt($date);
        $this->assertEquals($expected, $this->history->getCreatedAtDate());
    }

    /**
     * @dataProvider statusProvider
     * @param string $status
     * @param string $expected
     */
    public function testGetSystemCommentByStatus($status, $expected)
    {
        $this->assertEquals($expected, History::getSystemCommentByStatus($status));
    }

    public function statusProvider()
    {
        return [
            [Status::STATE_PENDING, __('We placed your Return request.')],
            [Status::STATE_AUTHORIZED, __('We have authorized your Return request.')],
            [Status::STATE_PARTIAL_AUTHORIZED, __('We partially authorized your Return request.')],
            [Status::STATE_RECEIVED, __('We received your Return request.')],
            [Status::STATE_RECEIVED_ON_ITEM, __('We partially received your Return request.')],
            [Status::STATE_APPROVED_ON_ITEM, __('We partially approved your Return request.')],
            [Status::STATE_REJECTED_ON_ITEM, __('We partially rejected your Return request.')],
            [Status::STATE_CLOSED, __('We closed your Return request.')],
            [Status::STATE_PROCESSED_CLOSED, __('We processed and closed your Return request.')]
        ];
    }
}
