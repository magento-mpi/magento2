<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cron\Model;

/**
 * Class \Magento\Cron\Model\ObserverTest
 */
class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Cron\Model\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\App\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var \Magento\Core\Model\App|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_app;

    /**
     * @var \Magento\Cron\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_config;

    /**
     * @var \Magento\Core\Model\Store\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreStoreConfig;

    /** @var \Magento\Cron\Model\Resource\Schedule\Collection|\PHPUnit_Framework_MockObject_MockObject */
    protected $_collection;

    /**
     * Prepare parameters
     */
    public function setUp()
    {
        $this->_objectManager = $this->getMockBuilder('Magento\App\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_app = $this->getMockBuilder('Magento\Core\Model\App')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_config = $this->getMockBuilder('Magento\Cron\Model\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_coreStoreConfig = $this->getMockBuilder('Magento\Core\Model\Store\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_collection = $this->getMockBuilder('Magento\Cron\Model\Resource\Schedule\Collection')
            ->setMethods(array('addFieldToFilter', 'load', '__wakeup'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_collection->expects($this->any())
            ->method('addFieldToFilter')
            ->will($this->returnSelf());
        $this->_collection->expects($this->any())
            ->method('load')
            ->will($this->returnSelf());

        $this->_observer = new \Magento\Cron\Model\Observer(
            $this->_objectManager, $this->_app, $this->_config, $this->_coreStoreConfig
        );
    }

    /**
     * Test case without saved cron jobs in data base
     */
    public function testDispatchNoPendingJobs()
    {
        $lastRun = time() + 10000000;
        $this->_app->expects($this->any())
            ->method('loadCache')
            ->will($this->returnValue($lastRun));
        $this->_coreStoreConfig->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue(0));

        $this->_config->expects($this->once())
            ->method('getJobs')
            ->will($this->returnValue(array()));

        $scheduleMock = $this->getMockBuilder('Magento\Cron\Model\Schedule')
            ->disableOriginalConstructor()
            ->getMock();
        $scheduleMock->expects($this->any())->method('getCollection')->will($this->returnValue($this->_collection));
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento\Cron\Model\Schedule'))
            ->will($this->returnValue($scheduleMock));

        $this->_observer->dispatch('');
    }

    /**
     * Test case for not existed cron jobs in files but in data base is presented
     */
    public function testDispatchNoJobConfig()
    {
        $lastRun = time() + 10000000;
        $this->_app->expects($this->any())
            ->method('loadCache')
            ->will($this->returnValue($lastRun));
        $this->_coreStoreConfig->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue(0));

        $schedule = $this->getMock('Magento\Cron\Model\Schedule', array('getJobCode', '__wakeup'), array(), '', false);
        $schedule->expects($this->once())
            ->method('getJobCode')
            ->will($this->returnValue('not_existed_job_code'));

        $this->_collection->addItem($schedule);

        $this->_config->expects($this->once())
            ->method('getJobs')
            ->will($this->returnValue(array()));

        $scheduleMock = $this->getMockBuilder('Magento\Cron\Model\Schedule')
            ->disableOriginalConstructor()
            ->getMock();
        $scheduleMock->expects($this->any())->method('getCollection')->will($this->returnValue($this->_collection));
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento\Cron\Model\Schedule'))
            ->will($this->returnValue($scheduleMock));

        $this->_observer->dispatch('');
    }

    /**
     * Test case checks if some job can't be locked
     */
    public function testDispatchCanNotLock()
    {
        $lastRun = time() + 10000000;
        $this->_app->expects($this->any())
            ->method('loadCache')
            ->will($this->returnValue($lastRun));
        $this->_coreStoreConfig->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue(0));

        $schedule = $this->getMockBuilder('Magento\Cron\Model\Schedule')
            ->setMethods(array('getJobCode', 'tryLockJob', 'getScheduledAt', '__wakeup'))
            ->disableOriginalConstructor()
            ->getMock();
        $schedule->expects($this->any())
            ->method('getJobCode')
            ->will($this->returnValue('test_job1'));
        $schedule->expects($this->once())
            ->method('getScheduledAt')
            ->will($this->returnValue('-1 day'));
        $schedule->expects($this->once())
            ->method('tryLockJob')
            ->will($this->returnValue(false));

        $this->_collection->addItem($schedule);

        $this->_config->expects($this->once())
            ->method('getJobs')
            ->will($this->returnValue(array('test_job1' => array('test_data'))));

        $scheduleMock = $this->getMockBuilder('Magento\Cron\Model\Schedule')
            ->disableOriginalConstructor()
            ->getMock();
        $scheduleMock->expects($this->any())->method('getCollection')->will($this->returnValue($this->_collection));
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento\Cron\Model\Schedule'))
            ->will($this->returnValue($scheduleMock));

        $this->_observer->dispatch('');
    }

    /**
     * Test case catch exception if to late for schedule
     */
    public function testDispatchExceptionTooLate()
    {
        $exceptionMessage = 'Too late for the schedule';

        $lastRun = time() + 10000000;
        $this->_app->expects($this->any())
            ->method('loadCache')
            ->will($this->returnValue($lastRun));
        $this->_coreStoreConfig->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue(0));

        $schedule = $this->getMockBuilder('Magento\Cron\Model\Schedule')
            ->setMethods(
                array('getJobCode', 'tryLockJob', 'getScheduledAt', 'save', 'setStatus', 'setMessages', '__wakeup')
            )
            ->disableOriginalConstructor()
            ->getMock();
        $schedule->expects($this->any())
            ->method('getJobCode')
            ->will($this->returnValue('test_job1'));
        $schedule->expects($this->once())
            ->method('getScheduledAt')
            ->will($this->returnValue('-1 day'));
        $schedule->expects($this->once())
            ->method('tryLockJob')
            ->will($this->returnValue(true));
        $schedule->expects($this->once())
            ->method('setStatus')
            ->with($this->equalTo(\Magento\Cron\Model\Schedule::STATUS_MISSED))
            ->will($this->returnSelf());
        $schedule->expects($this->once())
            ->method('setMessages')
            ->with($this->equalTo($exceptionMessage));
        $schedule->expects($this->once())
            ->method('save');

        $this->_collection->addItem($schedule);

        $this->_config->expects($this->once())
            ->method('getJobs')
            ->will($this->returnValue(array('test_job1' => array('test_data'))));

        $scheduleMock = $this->getMockBuilder('Magento\Cron\Model\Schedule')
            ->disableOriginalConstructor()
            ->getMock();
        $scheduleMock->expects($this->any())->method('getCollection')->will($this->returnValue($this->_collection));
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento\Cron\Model\Schedule'))
            ->will($this->returnValue($scheduleMock));

        $this->_observer->dispatch('');
    }

    /**
     * Test case catch exception if callback not exist
     */
    public function testDispatchExceptionNoCallback()
    {
        $exceptionMessage = 'No callbacks found';

        $schedule = $this->getMockBuilder('Magento\Cron\Model\Schedule')
            ->setMethods(
                array('getJobCode', 'tryLockJob', 'getScheduledAt', 'save', 'setStatus', 'setMessages', '__wakeup')
            )
            ->disableOriginalConstructor()
            ->getMock();
        $schedule->expects($this->any())
            ->method('getJobCode')
            ->will($this->returnValue('test_job1'));
        $schedule->expects($this->once())
            ->method('getScheduledAt')
            ->will($this->returnValue('-1 day'));
        $schedule->expects($this->once())
            ->method('tryLockJob')
            ->will($this->returnValue(true));
        $schedule->expects($this->once())
            ->method('setStatus')
            ->with($this->equalTo(\Magento\Cron\Model\Schedule::STATUS_ERROR))
            ->will($this->returnSelf());
        $schedule->expects($this->once())
            ->method('setMessages')
            ->with($this->equalTo($exceptionMessage));
        $schedule->expects($this->once())
            ->method('save');

        $this->_collection->addItem($schedule);

        $this->_config->expects($this->once())
            ->method('getJobs')
            ->will($this->returnValue(array('test_job1' => array('test_data'))));

        $lastRun = time() + 10000000;
        $this->_app->expects($this->any())
            ->method('loadCache')
            ->will($this->returnValue($lastRun));

        $this->_coreStoreConfig->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue(strtotime('+1 day')));

        $scheduleMock = $this->getMockBuilder('Magento\Cron\Model\Schedule')
            ->disableOriginalConstructor()
            ->getMock();
        $scheduleMock->expects($this->any())->method('getCollection')->will($this->returnValue($this->_collection));
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento\Cron\Model\Schedule'))
            ->will($this->returnValue($scheduleMock));

        $this->_observer->dispatch('');
    }

    /**
     * Test case catch exception if callback exists but can't be executed
     */
    public function testDispatchExceptionNotExecutable()
    {
        $jobConfig = array(
            'test_job1' => array(
                'instance' => 'Not_Existed_Class',
                'method' => 'notExistedMethod'
            )
        );
        $exceptionMessage = 'Invalid callback: Not_Existed_Class::notExistedMethod can\'t be called';

        $schedule = $this->getMockBuilder('Magento\Cron\Model\Schedule')
            ->setMethods(
                array('getJobCode', 'tryLockJob', 'getScheduledAt', 'save', 'setStatus', 'setMessages', '__wakeup')
            )
            ->disableOriginalConstructor()
            ->getMock();
        $schedule->expects($this->any())
            ->method('getJobCode')
            ->will($this->returnValue('test_job1'));
        $schedule->expects($this->once())
            ->method('getScheduledAt')
            ->will($this->returnValue('-1 day'));
        $schedule->expects($this->once())
            ->method('tryLockJob')
            ->will($this->returnValue(true));
        $schedule->expects($this->once())
            ->method('setStatus')
            ->with($this->equalTo(\Magento\Cron\Model\Schedule::STATUS_ERROR))
            ->will($this->returnSelf());
        $schedule->expects($this->once())
            ->method('setMessages')
            ->with($this->equalTo($exceptionMessage));
        $schedule->expects($this->once())
            ->method('save');

        $this->_collection->addItem($schedule);

        $this->_config->expects($this->once())
            ->method('getJobs')
            ->will($this->returnValue($jobConfig));

        $lastRun = time() + 10000000;
        $this->_app->expects($this->any())
            ->method('loadCache')
            ->will($this->returnValue($lastRun));
        $this->_coreStoreConfig->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue(strtotime('+1 day')));

        $scheduleMock = $this->getMockBuilder('Magento\Cron\Model\Schedule')
            ->disableOriginalConstructor()
            ->getMock();
        $scheduleMock->expects($this->any())->method('getCollection')->will($this->returnValue($this->_collection));
        $this->_objectManager->expects($this->at(0))
            ->method('create')
            ->with($this->equalTo('Magento\Cron\Model\Schedule'))
            ->will($this->returnValue($scheduleMock));
        $this->_objectManager->expects($this->at(1))
            ->method('create')
            ->with($this->equalTo('Not_Existed_Class'))
            ->will($this->returnValue(''));

        $this->_observer->dispatch('');
    }

    /**
     * Test case, successfully run job
     */
    public function testDispatchRunJob()
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'CronJob.php';
        $testCronJob = new \Magento\Cron\Model\CronJob();

        $jobConfig = array(
            'test_job1' => array(
                'instance' => 'CronJob',
                'method' => 'execute'
            )
        );

        $scheduleMethods = array(
            'getJobCode',
            'tryLockJob',
            'getScheduledAt',
            'save',
            'setStatus',
            'setMessages',
            'setExecutedAt',
            'setFinishedAt',
            '__wakeup',
        );
        $schedule = $this->getMockBuilder('Magento\Cron\Model\Schedule')
            ->setMethods($scheduleMethods)
            ->disableOriginalConstructor()
            ->getMock();
        $schedule->expects($this->any())
            ->method('getJobCode')
            ->will($this->returnValue('test_job1'));
        $schedule->expects($this->once())
            ->method('getScheduledAt')
            ->will($this->returnValue('-1 day'));
        $schedule->expects($this->once())
            ->method('tryLockJob')
            ->will($this->returnValue(true));

        // cron start to execute some job
        $schedule->expects($this->at(4))
            ->method('setStatus')
            ->with($this->equalTo(\Magento\Cron\Model\Schedule::STATUS_RUNNING))
            ->will($this->returnSelf());
        $schedule->expects($this->any())
            ->method('setExecutedAt')
            ->will($this->returnSelf());
        $schedule->expects($this->at(6))
            ->method('save');

        // cron end execute some job
        $schedule->expects($this->at(7))
            ->method('setStatus')
            ->with($this->equalTo(\Magento\Cron\Model\Schedule::STATUS_SUCCESS))
            ->will($this->returnSelf());

        $schedule->expects($this->at(9))
            ->method('save');

        $this->_collection->addItem($schedule);

        $this->_config->expects($this->once())
            ->method('getJobs')
            ->will($this->returnValue($jobConfig));

        $lastRun = time() + 10000000;
        $this->_app->expects($this->any())
            ->method('loadCache')
            ->will($this->returnValue($lastRun));
        $this->_coreStoreConfig->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue(strtotime('+1 day')));

        $scheduleMock = $this->getMockBuilder('Magento\Cron\Model\Schedule')
            ->disableOriginalConstructor()
            ->getMock();
        $scheduleMock->expects($this->any())->method('getCollection')->will($this->returnValue($this->_collection));
        $this->_objectManager->expects($this->at(0))
            ->method('create')
            ->with($this->equalTo('Magento\Cron\Model\Schedule'))
            ->will($this->returnValue($scheduleMock));
        $this->_objectManager->expects($this->at(1))
            ->method('create')
            ->with($this->equalTo('CronJob'))
            ->will($this->returnValue($testCronJob));

        $this->_observer->dispatch('');

        $this->assertInstanceOf('Magento\Cron\Model\Schedule', $testCronJob->getParam());
    }

    /**
     * Testing _generate(), iterate over saved cron jobs
     */
    public function testDispatchNotGenerate()
    {
        $this->_config->expects($this->at(0))
            ->method('getJobs')
            ->will($this->returnValue(array()));
        $this->_config->expects($this->at(1))
            ->method('getJobs')
            ->will($this->returnValue(array()));

        $this->_app->expects($this->at(0))
            ->method('loadCache')
            ->with($this->equalTo(\Magento\Cron\Model\Observer::CACHE_KEY_LAST_SCHEDULE_GENERATE_AT))
            ->will($this->returnValue(time() - 10000000));
        $this->_app->expects($this->at(2))
            ->method('loadCache')
            ->with($this->equalTo(\Magento\Cron\Model\Observer::CACHE_KEY_LAST_HISTORY_CLEANUP_AT))
            ->will($this->returnValue(time() + 10000000));

        $this->_coreStoreConfig->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue(0));

        $schedule = $this->getMockBuilder('Magento\Cron\Model\Schedule')
            ->setMethods(array('getJobCode', 'getScheduledAt', '__wakeup'))
            ->disableOriginalConstructor()
            ->getMock();
        $schedule->expects($this->any())
            ->method('getJobCode')
            ->will($this->returnValue('job_code1'));
        $schedule->expects($this->once())
            ->method('getScheduledAt')
            ->will($this->returnValue('* * * * *'));

        $this->_collection->addItem(new \Magento\Object());
        $this->_collection->addItem($schedule);

        $this->_app->expects($this->any())->method('saveCache');

        $scheduleMock = $this->getMockBuilder('Magento\Cron\Model\Schedule')
            ->disableOriginalConstructor()
            ->getMock();
        $scheduleMock->expects($this->any())->method('getCollection')->will($this->returnValue($this->_collection));
        $this->_objectManager->expects($this->any())
            ->method('create')
            ->with($this->equalTo('Magento\Cron\Model\Schedule'))
            ->will($this->returnValue($scheduleMock));

        $this->_observer->dispatch('');
    }

    /**
     * Testing _generate(), iterate over saved cron jobs and generate jobs
     */
    public function testDispatchGenerate()
    {
        $this->_config->expects($this->at(0))
            ->method('getJobs')
            ->will($this->returnValue(array()));
        $jobs = array(
            'job1' => array(
                'config_path' => 'test/path'
            ),
            'job2' => array(
                'schedule' => ''
            ),
            'job3' => array(
                'schedule' => '* * * * *'
            )
        );
        $this->_config->expects($this->at(1))
            ->method('getJobs')
            ->will($this->returnValue($jobs));

        $this->_app->expects($this->at(0))
            ->method('loadCache')
            ->with($this->equalTo(\Magento\Cron\Model\Observer::CACHE_KEY_LAST_SCHEDULE_GENERATE_AT))
            ->will($this->returnValue(time() - 10000000));
        $this->_app->expects($this->at(2))
            ->method('loadCache')
            ->with($this->equalTo(\Magento\Cron\Model\Observer::CACHE_KEY_LAST_HISTORY_CLEANUP_AT))
            ->will($this->returnValue(time() + 10000000));

        $this->_coreStoreConfig->expects($this->at(0))
            ->method('getConfig')
            ->will($this->returnValue(0));

        $scheduleMethods = array(
            'getJobCode',
            'getScheduledAt',
            'setJobCode',
            'setCronExpr',
            'setStatus',
            'trySchedule',
            'unsScheduleId',
            'save',
            '__wakeup'
        );
        $schedule = $this->getMockBuilder('Magento\Cron\Model\Schedule')
            ->setMethods($scheduleMethods)
            ->disableOriginalConstructor()
            ->getMock();
        $schedule->expects($this->any())
            ->method('getJobCode')
            ->will($this->returnValue('job_code1'));
        $schedule->expects($this->once())
            ->method('getScheduledAt')
            ->will($this->returnValue('* * * * *'));
        $schedule->expects($this->any())
            ->method('unsScheduleId')
            ->will($this->returnSelf());
        $schedule->expects($this->any())
            ->method('trySchedule')
            ->will($this->returnSelf());

        $this->_collection->addItem(new \Magento\Object());
        $this->_collection->addItem($schedule);

        $this->_app->expects($this->any())->method('saveCache');

        $scheduleMock = $this->getMockBuilder('Magento\Cron\Model\Schedule')
            ->disableOriginalConstructor()
            ->setMethods(array('getCollection', '__wakeup'))
            ->getMock();
        $scheduleMock->expects($this->any())->method('getCollection')->will($this->returnValue($this->_collection));
        $this->_objectManager->expects($this->any())
            ->method('create')
            ->with($this->equalTo('Magento\Cron\Model\Schedule'))
            ->will($this->returnValue($scheduleMock));

        $this->_observer->dispatch('');
    }

    /**
     * Test case without saved cron jobs in data base
     */
    public function testDispatchCleanup()
    {
        $schedule = $this->getMockBuilder('Magento\Cron\Model\Schedule')
            ->disableOriginalConstructor()
            ->setMethods(array('getExecutedAt', 'getStatus', 'delete', '__wakeup'))
            ->getMock();
        $schedule->expects($this->any())->method('getExecutedAt')->will($this->returnValue('-1 day'));
        $schedule->expects($this->any())->method('getStatus')->will($this->returnValue('success'));

        $this->_collection->addItem($schedule);

        $this->_config->expects($this->once())
            ->method('getJobs')
            ->will($this->returnValue(array()));

        $this->_app->expects($this->at(0))
            ->method('loadCache')
            ->will($this->returnValue(time() + 10000000));
        $this->_app->expects($this->at(1))
            ->method('loadCache')
            ->will($this->returnValue(time() - 10000000));

        $this->_coreStoreConfig->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue(0));

        $scheduleMock = $this->getMockBuilder('Magento\Cron\Model\Schedule')
            ->disableOriginalConstructor()
            ->getMock();
        $scheduleMock->expects($this->any())->method('getCollection')->will($this->returnValue($this->_collection));
        $this->_objectManager->expects($this->at(0))
            ->method('create')
            ->with($this->equalTo('Magento\Cron\Model\Schedule'))
            ->will($this->returnValue($scheduleMock));

        $collection = $this->getMockBuilder('Magento\Cron\Model\Resource\Schedule\Collection')
            ->setMethods(array('addFieldToFilter', 'load', '__wakeup'))
            ->disableOriginalConstructor()
            ->getMock();
        $collection->expects($this->any())
            ->method('addFieldToFilter')
            ->will($this->returnSelf());
        $collection->expects($this->any())
            ->method('load')
            ->will($this->returnSelf());
        $collection->addItem($schedule);

        $scheduleMock = $this->getMockBuilder('Magento\Cron\Model\Schedule')
            ->disableOriginalConstructor()
            ->getMock();
        $scheduleMock->expects($this->any())->method('getCollection')->will($this->returnValue($collection));
        $this->_objectManager->expects($this->at(1))
            ->method('create')
            ->with($this->equalTo('Magento\Cron\Model\Schedule'))
            ->will($this->returnValue($scheduleMock));

        $this->_observer->dispatch('');
    }
}
