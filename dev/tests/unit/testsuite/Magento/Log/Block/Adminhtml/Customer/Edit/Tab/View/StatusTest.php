<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Log\Block\Adminhtml\Edit\Tab\View;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class StatusTest
 * @package Magento\Log\Block\Adminhtml\Edit\Tab\View
 */
class StatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Log\Block\Adminhtml\Customer\Edit\Tab\View\Status
     */
    protected $block;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $logFactory;

    /**
     * @var \Magento\Log\Model\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerLog;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeDate;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfig;

    protected function setUp()
    {
        $log = $this->getMock('Magento\Log\Model\Log', ['getOnlineMinutesInterval'], [], '', false);
        $log->expects($this->any())->method('getOnlineMinutesInterval')->will($this->returnValue(1));

        $this->customerLog = $this->getMockBuilder('Magento\Log\Model\Customer')->disableOriginalConstructor()
            ->setMethods(['getLoginAtTimestamp', 'loadByCustomer', 'getLogoutAt', 'getLastVisitAt'])
            ->getMock();
        $this->customerLog->expects($this->any())->method('loadByCustomer')->will($this->returnSelf());

        $this->logFactory = $this->getMockBuilder('Magento\Log\Model\CustomerFactory')->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->logFactory->expects($this->any())->method('create')->will($this->returnValue($this->customerLog));

        $dateTime = $this->getMock('Magento\Framework\Stdlib\DateTime');

        $customer = $this->getMockBuilder('\Magento\Customer\Service\V1\Data\Customer')
            ->setMethods(['getId', 'getStoreId'])
            ->disableOriginalConstructor()->getMock();
        $customer->expects($this->any())->method('getId')->will($this->returnValue(1));
        $customer->expects($this->any())->method('getStoreId')->will($this->returnValue(1));

        $customerData = array('account' => ['id' => 1, 'store_id' => 1]);
        $customerBuilder = $this->getMockBuilder('\Magento\Customer\Service\V1\Data\CustomerBuilder')
            ->setMethods(['populateWithArray', 'create'])
            ->disableOriginalConstructor()
            ->getMock();
        $customerBuilder->expects($this->any())->method('populateWithArray')
            ->will($this->returnSelf());
        $customerBuilder->expects($this->any())->method('create')
            ->will($this->returnValue($customer));

        $backendSession = $this->getMockBuilder('\Magento\Backend\Model\Session')
            ->setMethods(['getCustomerData'])->disableOriginalConstructor()->getMock();
        $backendSession->expects($this->any())->method('getCustomerData')->will($this->returnValue($customerData));

        $this->localeDate = $this->getMockBuilder('Magento\Framework\Stdlib\DateTime\Timezone')
            ->setMethods(['scopeDate', 'formatDate', 'getDefaultTimezonePath'])
            ->disableOriginalConstructor()->getMock();
        $this->localeDate->expects($this->any())->method('getDefaultTimezonePath')
            ->will($this->returnValue('path/to/default/timezone'));

        $this->scopeConfig = $this->getMockBuilder('Magento\Framework\App\Config')
            ->setMethods(['getValue'])
            ->disableOriginalConstructor()->getMock();

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->block = $objectManagerHelper->getObject(
            'Magento\Log\Block\Adminhtml\Customer\Edit\Tab\View\Status',
            [
                'logFactory' => $this->logFactory,
                'localeDate' => $this->localeDate,
                'scopeConfig' => $this->scopeConfig,
                'modelLog' => $log,
                'dateTime' => $dateTime,
                'customerBuilder' => $customerBuilder,
                'backendSession' => $backendSession
            ]
        );
    }

    public function testGetCustomerLog()
    {
        $this->logFactory->expects($this->once())->method('create')->will($this->returnValue($this->customerLog));
        $this->assertSame($this->customerLog, $this->block->getCustomerLog());
    }

    public function testGetCurrentStatusOffline()
    {
        $date = date('Y-m-d H:i:s');
        $this->customerLog->expects($this->any())->method('getLogoutAt')->will($this->returnValue($date));
        $this->assertEquals('Offline', $this->block->getCurrentStatus());
    }

    public function testGetCurrentStatusOnline()
    {
        $this->customerLog->expects($this->any())->method('getLogoutAt')->will($this->returnValue(0));
        $this->customerLog->expects($this->any())->method('getLastVisitAt')->will($this->returnValue(time()));
        $this->assertEquals('Online', $this->block->getCurrentStatus());
    }

    public function testGetLastLoginDate()
    {
        $date = date('Y-m-d H:i:s');
        $time = strtotime($date);
        $this->customerLog->expects($this->any())->method('getLoginAtTimestamp')->will($this->returnValue($time));
        $this->localeDate->expects($this->once())->method('formatDate')->will($this->returnValue($date));
        $this->assertEquals($date, $this->block->getLastLoginDate());
    }

    public function testAfterGetLastLoginDateNever()
    {
        $this->assertEquals('Never', $this->block->getLastLoginDate());
    }

    public function testGetStoreLastLoginDate()
    {
        $date = date('Y-m-d H:i:s');
        $time = strtotime($date);

        $this->localeDate->expects($this->once())->method('scopeDate')->will($this->returnValue($date));
        $this->localeDate->expects($this->once())->method('formatDate')->will($this->returnValue($date));

        $this->customerLog->expects($this->any())->method('getLoginAtTimestamp')->will($this->returnValue($time));
        $this->assertEquals($date, $this->block->getStoreLastLoginDate());
    }

    public function testGetStoreLastLoginDateNever()
    {
        $this->assertEquals('Never', $this->block->getStoreLastLoginDate());
    }

    public function testGetStoreLastLoginDateTimezone()
    {
        $this->scopeConfig->expects($this->once())->method('getValue')
            ->with('path/to/default/timezone', 'store', 1)
            ->will($this->returnValue('America/Los_Angeles'));
        $this->block->getStoreLastLoginDateTimezone();
    }
}
