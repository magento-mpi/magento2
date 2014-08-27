<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Log\Block\Adminhtml\Edit\Tab\View;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Log\Block\Adminhtml\Edit\Tab\View\Plugin
     */
    protected $plugin;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $logFactory;

    /**
     * @var \Magento\Log\Model\Log|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $log;

    /**
     * @var \Magento\Log\Model\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerLog;

    /**
     * @var \Magento\Framework\Stdlib\DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dateTime;

    /**
     * @var \Magento\Customer\Block\Adminhtml\Edit\Tab\View
     */
    protected $object;

    protected function setUp()
    {
        $this->log = $this->getMock('Magento\Log\Model\Log', ['getOnlineMinutesInterval'], [], '', false);
        $this->log->expects($this->any())->method('getOnlineMinutesInterval')->will($this->returnValue(1));

        $this->customerLog = $this->getMockBuilder('Magento\Log\Model\Customer')->disableOriginalConstructor()
            ->setMethods(['getLoginAtTimestamp', 'loadByCustomer', 'getLogoutAt', 'getLastVisitAt'])
            ->getMock();
        $this->customerLog->expects($this->once())->method('loadByCustomer')->will($this->returnSelf());

        $this->logFactory = $this->getMockBuilder('Magento\Log\Model\CustomerFactory')->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->logFactory->expects($this->once())->method('create')->will($this->returnValue($this->customerLog));

        $this->dateTime = $this->getMock('Magento\Framework\Stdlib\DateTime');

        $this->object = $this->getMockBuilder('Magento\Customer\Block\Adminhtml\Edit\Tab\View')
            ->disableOriginalConstructor()
            ->setMethods(['formatDate', 'getCustomerId', 'getCustomer'])
            ->getMock();
        $this->object->expects($this->once())->method('getCustomerId')->will($this->returnValue(1));

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $arguments = $this->objectManagerHelper
            ->getConstructArguments(
                'Magento\Log\Block\Adminhtml\Edit\Tab\View\Plugin',
                [
                    'logFactory' => $this->logFactory,
                    'modelLog' => $this->log,
                    'dateTime' => $this->dateTime
                ]
            );

        $localeDate = $arguments['context']->getLocaleDate();
        $localeDate->expects($this->any())->method('scopeDate')->will($this->returnArgument(1));

        $this->plugin = $this->objectManagerHelper->getObject(
            'Magento\Log\Block\Adminhtml\Edit\Tab\View\Plugin', $arguments

        );
    }

    public function testGetCustomerLog()
    {
        $this->logFactory->expects($this->once())->method('create')->will($this->returnValue($this->customerLog));
        $this->assertSame($this->customerLog, $this->plugin->getCustomerLog($this->object));
    }

    public function testAfterGetCurrentStatusOffline()
    {
        $date = date('Y-m-d H:i:s');
        $this->customerLog->expects($this->any())->method('getLogoutAt')->will($this->returnValue($date));
        $this->assertEquals('Offline', $this->plugin->afterGetCurrentStatus($this->object));
    }

    public function testAfterGetCurrentStatusOnline()
    {
        $this->customerLog->expects($this->any())->method('getLogoutAt')->will($this->returnValue(0));
        $this->customerLog->expects($this->any())->method('getLastVisitAt')->will($this->returnValue(time()));
        $this->assertEquals('Online', $this->plugin->afterGetCurrentStatus($this->object));
    }

    public function testAfterGetLastLoginDate()
    {
        $date = date('Y-m-d H:i:s');
        $time = strtotime($date);
        $this->customerLog->expects($this->any())->method('getLoginAtTimestamp')->will($this->returnValue($time));
        $this->object->expects($this->once())->method('formatDate')->with($time)->will($this->returnValue($date));
        $this->assertEquals($date, $this->plugin->afterGetLastLoginDate($this->object));
    }

    public function testAfterGetLastLoginDateNever()
    {
        $this->assertEquals('Never', $this->plugin->afterGetLastLoginDate($this->object));
    }

    public function testAfterGetStoreLastLoginDate()
    {
        $date = date('Y-m-d H:i:s');
        $time = strtotime($date);
        $customer = $this->getMock('Magento\Customer\Model\Customer', ['getStoreId'], [], '', false, false);
        $customer->expects($this->once())->method('getStoreId')->will($this->returnValue(1));

        $this->customerLog->expects($this->any())->method('getLoginAtTimestamp')->will($this->returnValue($time));
        $this->object->expects($this->once())->method('getCustomer')->will($this->returnValue($customer));
        $this->object->expects($this->once())->method('formatDate')->with($time)->will($this->returnValue($date));

        $this->assertEquals($date, $this->plugin->afterGetStoreLastLoginDate($this->object));
    }

    public function testAfterGetStoreLastLoginDateNever()
    {
        $this->assertEquals('Never', $this->plugin->afterGetStoreLastLoginDate($this->object));
    }
}
