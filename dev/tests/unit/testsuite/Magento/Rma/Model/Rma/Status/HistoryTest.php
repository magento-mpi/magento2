<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Model\Rma\Status;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Rma\Model\Rma\Source\Status;

/**
 * Class HistoryTest
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @var \Magento\Store\Model\StoreManagerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

    /**
     * @var \Magento\Sales\Model\Order | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $order;

    /**
     * @var TimezoneInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeDate;

    /**
     * @var \Magento\Rma\Model\RmaFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaFactory;

    /**
     * @var \Magento\Rma\Model\Rma | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rma;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->eventManager = $this->getMock('Magento\Framework\Event\Manager', [], [], '', false);
        $this->storeManager = $this->getMock('Magento\Store\Model\StoreManagerInterface', [], [], '', false);

        $context = $this->getMock('Magento\Framework\Model\Context', [], [], '', false);
        $context->expects($this->once())->method('getEventDispatcher')->will($this->returnValue($this->eventManager));

        $this->rmaConfig = $this->getMock(
            'Magento\Rma\Model\Config',
            [
                '__wakeup',
                'getRootCommentEmail',
                'getCustomerEmailRecipient',
                'getRootCustomerCommentEmail',
                'init',
                'isEnabled',
                'getCopyTo',
                'getCopyMethod',
                'getGuestTemplate',
                'getTemplate',
                'getIdentity',
                'getRootRmaEmail',
                'getRootAuthEmail',

            ],
            [],
            '',
            false
        );
        $this->rma = $this->getMock(
            'Magento\Rma\Model\Rma',
            [
                '__wakeup',
                'getId',
                'getStatus',
                'getStoreId',
                'getOrder',
                'getItemsForDisplay',
                'load'
            ],
            [],
            '',
            false
        );
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
        $this->order = $this->getMock(
            'Magento\Sales\Model\Order',
            ['getStore', 'getBillingAddress', '__wakeup'],
            [],
            '',
            false
        );
        $this->rmaFactory = $this->getMock('Magento\Rma\Model\RmaFactory', ['create', '__wakeup'], [], '', false);

        $this->history = $objectManagerHelper->getObject(
            'Magento\Rma\Model\Rma\Status\History',
            [
                'storeManager' => $this->storeManager,
                'rmaFactory' => $this->rmaFactory,
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

    public function testGetStore()
    {
        $store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $this->order->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($store));
        $this->history->setOrder($this->order);

        $this->assertEquals($store, $this->history->getStore());
    }

    public function testGetRma()
    {
        $this->history->unsetData('rma');
        $this->history->setData('rma_entity_id', 10003);
        $this->rmaFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->rma));
        $this->rma->expects($this->once())
            ->method('load')
            ->with($this->history->getRmaEntityId())
            ->will($this->returnSelf());
        $this->assertEquals($this->rma, $this->history->getRma());
    }

    public function testGetStoreNoOrder()
    {
        $store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $this->storeManager->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($store));
        $this->assertEquals($store, $this->history->getStore());
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

        $this->prepareSaveComment($id, $status, $date, $emailSent);

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
        $this->rma->expects($this->once())
            ->method('getStoreId')
            ->will($this->returnValue($storeId));
        $this->rma->expects($this->once())
            ->method('getOrder')
            ->will($this->returnValue($this->order));

        $this->rmaConfig->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));

        $this->prepareTransportBuilder();

        $this->history->setRma($this->rma);
        $this->assertNull($this->history->getEmailSent());
        $this->history->sendNewRmaEmail();
        $this->assertTrue($this->history->getEmailSent());
    }

    public function testSendAuthorizeEmail()
    {
        $storeId = 5;
        $customerEmail = 'custom@email.com';
        $name = 'name';

        $this->prepareRmaModel($storeId, $name, $customerEmail);
        $this->prepareRmaConfig('bcc');
        $this->prepareTransportBuilder();

        $this->order->setCustomerEmail($customerEmail);
        $this->order->setCustomerIsGuest(false);
        $this->history->setRma($this->rma);
        $this->assertNull($this->history->getEmailSent());
        $this->history->sendAuthorizeEmail();
        $this->assertTrue($this->history->getEmailSent());
    }

    public function testSendAuthorizeEmailGuest()
    {
        $storeId = 5;
        $customerEmail = 'custom@email.com';
        $name = 'name';

        $this->prepareRmaModel($storeId, $name, $customerEmail);
        $this->prepareRmaConfig('copy');
        $this->prepareTransportBuilder();

        $this->order->setCustomerIsGuest(true);
        $address = $this->getMock('Magento\Sales\Model\Order\Address', [], [], '', false);
        $address->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name));
        $this->order->expects($this->once())
            ->method('getBillingAddress')
            ->will($this->returnValue($address));

        $this->history->sendAuthorizeEmail();
        $this->assertTrue($this->history->getEmailSent());
    }

    protected function prepareTransportBuilder()
    {
        $this->transportBuilder->expects($this->atLeastOnce())
            ->method('setTemplateIdentifier')
            ->will($this->returnSelf());
        $this->transportBuilder->expects($this->atLeastOnce())
            ->method('setTemplateOptions')
            ->will($this->returnSelf());
        $this->transportBuilder->expects($this->atLeastOnce())
            ->method('setTemplateVars')
            ->will($this->returnSelf());
        $this->transportBuilder->expects($this->atLeastOnce())
            ->method('setFrom')
            ->will($this->returnSelf());
        $this->transportBuilder->expects($this->atLeastOnce())
            ->method('addTo')
            ->will($this->returnSelf());
        $this->transportBuilder->expects($this->atLeastOnce())
            ->method('addBcc')
            ->will($this->returnSelf());

        $transport = $this->getMock('Magento\Framework\Mail\Transport', [], [], '', false);
        $transport->expects($this->atLeastOnce())
            ->method('sendMessage');

        $this->transportBuilder->expects($this->atLeastOnce())
            ->method('getTransport')
            ->will($this->returnValue($transport));
    }

    /**
     * @param string $copyMethod
     */
    protected function prepareRmaConfig($copyMethod)
    {
        $template = 'some html';
        $this->rmaConfig->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));
        if ($copyMethod == 'bcc') {
            $copyTo = 'copyTo';
        } else {
            $copyTo = ['email@com.com'];
        }
        $this->rmaConfig->expects($this->once())
            ->method('getCopyTo')
            ->will($this->returnValue($copyTo));
        $this->rmaConfig->expects($this->once())
            ->method('getCopyMethod')
            ->will($this->returnValue($copyMethod));
        if ($this->order->getCustomerIsGuest()) {
            $this->rmaConfig->expects($this->once())
                ->method('getGuestTemplate')
                ->will($this->returnValue($template));
        }
    }

    /**
     * @param $storeId
     * @param $name
     * @param $customerEmail
     */
    protected function prepareRmaModel($storeId, $name, $customerEmail)
    {
        $this->rma->expects($this->atLeastOnce())
            ->method('getStoreId')
            ->will($this->returnValue($storeId));
        $this->rma->expects($this->atLeastOnce())
            ->method('getOrder')
            ->will($this->returnValue($this->order));
        $this->rma->setCustomerName($name);
        $this->rma->setCustomerCustomEmail($customerEmail);
        $this->rma->setIsSendAuthEmail(true);
        $this->history->setRma($this->rma);
    }

    public function testSendCommentEmail()
    {
        $storeId = 5;
        $customerEmail = 'custom@email.com';
        $name = 'name';

        $this->prepareRmaModel($storeId, $name, $customerEmail);
        $this->prepareRmaConfig('bcc');
        $this->prepareTransportBuilder();

        $this->order->setCustomerEmail($customerEmail);
        $this->order->setCustomerName($name);
        $this->order->setCustomerIsGuest(false);
        $this->history->setRma($this->rma);
        $this->assertNull($this->history->getEmailSent());
        $this->history->sendCommentEmail();
        $this->assertTrue($this->history->getEmailSent());
    }

    public function testSendCommentEmailGuest()
    {
        $storeId = 5;
        $customerEmail = 'custom@email.com';
        $name = 'name';

        $this->prepareRmaModel($storeId, $name, $customerEmail);
        $this->prepareRmaConfig('copy');
        $this->prepareTransportBuilder();

        $address = $this->getMock('Magento\Sales\Model\Order\Address', [], [], '', false);
        $address->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name));
        $this->order->expects($this->once())
            ->method('getBillingAddress')
            ->will($this->returnValue($address));

        $this->order->setCustomerEmail($customerEmail);
        $this->order->setCustomerName($name);
        $this->order->setCustomerIsGuest(true);
        $this->history->setRma($this->rma);
        $this->assertNull($this->history->getEmailSent());
        $this->history->sendCommentEmail();
        $this->assertTrue($this->history->getEmailSent());
    }

    public function testSendCustomerCommentEmail()
    {
        $storeId = 5;
        $customerEmail = 'custom@email.com';
        $name = 'name';
        $commentRoot = 'sales_email/magento_rma_customer_comment';

        $this->prepareRmaModel($storeId, $name, $customerEmail);
        $this->prepareRmaConfig('bcc');
        $this->rmaConfig->expects($this->once())
            ->method('getCustomerEmailRecipient')
            ->with($storeId)
            ->will($this->returnValue($customerEmail));
        $this->rmaConfig->expects($this->once())
            ->method('getRootCustomerCommentEmail')
            ->will($this->returnValue($commentRoot));
        $this->prepareTransportBuilder();

        $this->order->setCustomerIsGuest(false);
        $this->history->setRma($this->rma);
        $this->assertNull($this->history->getEmailSent());
        $this->history->sendCustomerCommentEmail();
        $this->assertTrue($this->history->getEmailSent());
    }

    public function testSendCustomerCommentEmailDisabled()
    {
        $this->history->setRma($this->rma);
        $this->rmaConfig->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(false));
        $this->assertEquals($this->history, $this->history->sendCustomerCommentEmail());
    }

    public function testSendAuthorizeEmailNotSent()
    {
        $this->history->setRma($this->rma);
        $this->rma->setIsSendAuthEmail(false);
        $this->assertEquals($this->history, $this->history->sendAuthorizeEmail());
        $this->assertNull($this->history->getEmailSent());
    }

    public function testSendRmaEmailWithItemsDisabled()
    {
        $this->history->setRma($this->rma);
        $this->rma->setIsSendAuthEmail(true);
        $this->rmaConfig->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(false));
        $this->assertEquals($this->history, $this->history->sendAuthorizeEmail());
    }

    public function testSendAuthorizeEmailFail()
    {
        $this->history->setRma($this->rma);
        $this->rma->setIsSendAuthEmail(false);
        $this->assertEquals($this->history, $this->history->sendAuthorizeEmail());
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

    /**
     * @param $id
     * @param $status
     * @param $date
     * @param $emailSent
     */
    protected function prepareSaveComment($id, $status, $date, $emailSent)
    {
        $this->rma->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($id));
        $this->rma->expects($this->atLeastOnce())
            ->method('getStatus')
            ->will($this->returnValue($status));

        $this->dateTimeDateTime->expects($this->once())
            ->method('gmtDate')
            ->will($this->returnValue($date));

        $this->resource->expects($this->once())
            ->method('save')
            ->with($this->history);

        $this->history->setRma($this->rma);
        $this->history->setEmailSent($emailSent);
    }

    public function testSaveSystemComment()
    {
        $id = 1;
        $status = 'status';
        $emailSent = true;
        $date = 'today';
        $this->rma->setStatus($status);
        $this->prepareSaveComment($id, $status, $date, $emailSent);

        $this->history->saveSystemComment();

        $this->assertEquals($emailSent, $this->history->getIsCustomerNotified());
        $this->assertEquals($date, $this->history->getCreatedAt());
        $this->assertEquals($status, $this->history->getStatus());
    }

    public function testSaveSystemCommentFailed()
    {
        $this->history->setData('rma', $this->rma);
        $this->rma->setOrigData('status', Status::STATE_PENDING);
        $this->rma->expects($this->once())
            ->method('getStatus')
            ->will($this->returnValue(Status::STATE_PENDING));

        $this->assertNull($this->history->saveSystemComment());
        $this->assertNull($this->history->getComment());
        $this->assertNull($this->history->getIsVisibleOnFront());
        $this->assertNull($this->history->getIsAdmin());
    }
}
