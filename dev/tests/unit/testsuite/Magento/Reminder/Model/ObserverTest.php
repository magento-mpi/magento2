<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Model;

use Magento\TestFramework\Helper\ObjectManager;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Observer
     */
    private $model;

    /**
     * @var \Magento\Reminder\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    private $reminderData;

    /**
     * @var \Magento\Reminder\Model\RuleFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleFactory;

    /**
     * @var \Magento\Reminder\Model\Rule|\PHPUnit_Framework_MockObject_MockObject
     */
    private $rule;

    /**
     * @var \Magento\Framework\Event\Observer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventObserver;

    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $this->reminderData = $this->getMockBuilder('Magento\Reminder\Helper\Data')
            ->setMethods(['isEnabled'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->rule = $this->getMockBuilder('Magento\Reminder\Model\Rule')
            ->setMethods(['sendReminderEmails', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->ruleFactory = $this->getMockBuilder('Magento\Reminder\Model\RuleFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->ruleFactory->expects($this->any())->method('create')->will($this->returnValue($this->rule));

        $this->eventObserver = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->setMethods(['getCollection', 'getRule'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $helper->getObject(
            'Magento\Reminder\Model\Observer',
            ['reminderData' => $this->reminderData, 'ruleFactory' => $this->ruleFactory]
        );
    }

    public function testAddSalesRuleFilter()
    {
        $collection = $this->getMockBuilder('Magento\SalesRule\Model\Resource\Rule\Collection')
            ->setMethods(['addAllowedSalesRulesFilter'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventObserver->expects($this->once())->method('getCollection')->will($this->returnValue($collection));
        $collection->expects($this->once())->method('addAllowedSalesRulesFilter');

        $this->model->addSalesRuleFilter($this->eventObserver);
    }

    public function testScheduledNotification()
    {
        $this->reminderData->expects($this->once())->method('isEnabled')->will($this->returnValue(true));

        $this->rule->expects($this->once())->method('sendReminderEmails');

        $result = $this->model->scheduledNotification();

        $this->assertEquals($this->model, $result);
    }

    public function testScheduledNotificationDisabled()
    {
        $this->reminderData->expects($this->once())->method('isEnabled')->will($this->returnValue(false));

        $this->model->scheduledNotification();
    }

    public function testGetCronMinutes()
    {
        $expected = [
            5 => __('5 minutes'),
            10 => __('10 minutes'),
            15 => __('15 minutes'),
            20 => __('20 minutes'),
            30 => __('30 minutes')
        ];

        $this->assertEquals($expected, $this->model->getCronMinutes());
    }

    public function testGetCronFrequencyTypes()
    {
        $expected = [
            Observer::CRON_MINUTELY => __('Minute Intervals'),
            Observer::CRON_HOURLY => __('Hourly'),
            Observer::CRON_DAILY => __('Daily')
        ];

        $this->assertEquals($expected, $this->model->getCronFrequencyTypes());
    }
}
