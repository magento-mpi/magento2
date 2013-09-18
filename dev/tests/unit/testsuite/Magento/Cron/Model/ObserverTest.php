<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class Magento_Cron_Model_ObserverTest
 */
class Magento_Cron_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Cron_Model_Observer
     */
    protected $_observer;

    /**
     * @var Magento_Core_Model_ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var Magento_Core_Model_App|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_app;

    /**
     * @var Magento_Cron_Model_Config|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_config;

    /**
     * @var Magento_Core_Model_Store_Config|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreStoreConfig;

    /**
     * Prepare parameters
     */
    public function setUp()
    {
        $this->_objectManager = $this->getMockBuilder('Magento_Core_Model_ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();
        $this->_app = $this->getMockBuilder('Magento_Core_Model_App')
            ->disableOriginalConstructor()
            ->setMethods(array('loadCache', 'saveCache'))
            ->getMock();
        $this->_config = $this->getMockBuilder('Magento_Cron_Model_Config')
            ->disableOriginalConstructor()
            ->setMethods(array('getJobs'))
            ->getMock();
        $this->_coreStoreConfig = $this->getMockBuilder('Magento_Core_Model_Store_Config')
            ->disableOriginalConstructor()
            ->setMethods(array('getConfig'))
            ->getMock();

        $this->_observer = new Magento_Cron_Model_Observer(
            $this->_objectManager, $this->_app, $this->_config, $this->_coreStoreConfig
        );
    }

    /**
     * Test case without saved cron jobs in data base
     */
    public function testDispatchNoPendingJobs()
    {
        $collection = $this->_getCollection();

        $this->_config->expects($this->once())
            ->method('getJobs')
            ->will($this->returnValue(array()));

        $this->_mockTime();

        $scheduleMock = $this->getMockBuilder('Magento_Cron_Model_Schedule')
            ->disableOriginalConstructor()
            ->setMethods(array('getCollection'))
            ->getMock();
        $scheduleMock->expects($this->any())->method('getCollection')->will($this->returnValue($collection));
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento_Cron_Model_Schedule'))
            ->will($this->returnValue($scheduleMock));

        $this->_observer->dispatch('');
    }

    /**
     * Test case for not existed cron jobs in files but in data base is presented
     */
    public function testDispatchNoJobConfig()
    {

        $schedule = $this->getMockBuilder('Magento_Cron_Model_Schedule')
            ->setMethods(array('getJobCode'))
            ->disableOriginalConstructor()
            ->getMock();
        $schedule->expects($this->once())
            ->method('getJobCode')
            ->will($this->returnValue('not_existed_job_code'));

        $collection = $this->_getCollection(array($schedule));

        $this->_config->expects($this->once())
            ->method('getJobs')
            ->will($this->returnValue(array()));

        $this->_mockTime();

        $scheduleMock = $this->getMockBuilder('Magento_Cron_Model_Schedule')
            ->disableOriginalConstructor()
            ->setMethods(array('getCollection'))
            ->getMock();
        $scheduleMock->expects($this->any())->method('getCollection')->will($this->returnValue($collection));
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento_Cron_Model_Schedule'))
            ->will($this->returnValue($scheduleMock));

        $this->_observer->dispatch('');
    }

    /**
     * Test case checks if some job can't be locked
     */
    public function testDispatchCanNotLock()
    {
        $schedule = $this->getMockBuilder('Magento_Cron_Model_Schedule')
            ->setMethods(array('getJobCode', 'tryLockJob', 'getScheduledAt'))
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

        $collection = $this->_getCollection(array($schedule));

        $this->_config->expects($this->once())
            ->method('getJobs')
            ->will($this->returnValue(array('test_job1' => array('test_data'))));

        $this->_mockTime();

        $scheduleMock = $this->getMockBuilder('Magento_Cron_Model_Schedule')
            ->disableOriginalConstructor()
            ->setMethods(array('getCollection'))
            ->getMock();
        $scheduleMock->expects($this->any())->method('getCollection')->will($this->returnValue($collection));
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento_Cron_Model_Schedule'))
            ->will($this->returnValue($scheduleMock));

        $this->_observer->dispatch('');
    }

    /**
     * Test case catch exception if to late for schedule
     */
    public function testDispatchExceptionTooLate()
    {
        $exceptionMessage = 'Too late for the schedule';

        $schedule = $this->getMockBuilder('Magento_Cron_Model_Schedule')
            ->setMethods(
                array('getJobCode', 'tryLockJob', 'getScheduledAt', 'save', 'setStatus', 'setMessages')
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
            ->with($this->equalTo(Magento_Cron_Model_Schedule::STATUS_MISSED))
            ->will($this->returnSelf());
        $schedule->expects($this->once())
            ->method('setMessages')
            ->with($this->equalTo($exceptionMessage));
        $schedule->expects($this->once())
            ->method('save');

        $collection = $this->_getCollection(array($schedule));

        $this->_config->expects($this->once())
            ->method('getJobs')
            ->will($this->returnValue(array('test_job1' => array('test_data'))));

        $this->_mockTime();

        $scheduleMock = $this->getMockBuilder('Magento_Cron_Model_Schedule')
            ->disableOriginalConstructor()
            ->setMethods(array('getCollection'))
            ->getMock();
        $scheduleMock->expects($this->any())->method('getCollection')->will($this->returnValue($collection));
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento_Cron_Model_Schedule'))
            ->will($this->returnValue($scheduleMock));

        $this->_observer->dispatch('');
    }

    /**
     * Test case catch exception if callback not exist
     */
    public function testDispatchExceptionNoCallback()
    {
        $exceptionMessage = 'No callbacks found';

        $schedule = $this->getMockBuilder('Magento_Cron_Model_Schedule')
            ->setMethods(
                array('getJobCode', 'tryLockJob', 'getScheduledAt', 'save', 'setStatus', 'setMessages')
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
            ->with($this->equalTo(Magento_Cron_Model_Schedule::STATUS_ERROR))
            ->will($this->returnSelf());
        $schedule->expects($this->once())
            ->method('setMessages')
            ->with($this->equalTo($exceptionMessage));
        $schedule->expects($this->once())
            ->method('save');

        $collection = $this->_getCollection(array($schedule));

        $this->_config->expects($this->once())
            ->method('getJobs')
            ->will($this->returnValue(array('test_job1' => array('test_data'))));

        $this->_mockTime(strtotime('+1 day'));

        $scheduleMock = $this->getMockBuilder('Magento_Cron_Model_Schedule')
            ->disableOriginalConstructor()
            ->setMethods(array('getCollection'))
            ->getMock();
        $scheduleMock->expects($this->any())->method('getCollection')->will($this->returnValue($collection));
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento_Cron_Model_Schedule'))
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

        $schedule = $this->getMockBuilder('Magento_Cron_Model_Schedule')
            ->setMethods(
                array('getJobCode', 'tryLockJob', 'getScheduledAt', 'save', 'setStatus', 'setMessages')
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
            ->with($this->equalTo(Magento_Cron_Model_Schedule::STATUS_ERROR))
            ->will($this->returnSelf());
        $schedule->expects($this->once())
            ->method('setMessages')
            ->with($this->equalTo($exceptionMessage));
        $schedule->expects($this->once())
            ->method('save');

        $collection = $this->_getCollection(array($schedule));

        $this->_config->expects($this->once())
            ->method('getJobs')
            ->will($this->returnValue($jobConfig));

        $this->_mockTime(strtotime('+1 day'));

        $scheduleMock = $this->getMockBuilder('Magento_Cron_Model_Schedule')
            ->disableOriginalConstructor()
            ->setMethods(array('getCollection'))
            ->getMock();
        $scheduleMock->expects($this->any())->method('getCollection')->will($this->returnValue($collection));
        $this->_objectManager->expects($this->at(0))
            ->method('create')
            ->with($this->equalTo('Magento_Cron_Model_Schedule'))
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
        $testCronJob = new Magento_Cron_Model_CronJob();

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
            'setFinishedAt'
        );
        $schedule = $this->getMockBuilder('Magento_Cron_Model_Schedule')
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
            ->with($this->equalTo(Magento_Cron_Model_Schedule::STATUS_RUNNING))
            ->will($this->returnSelf());
        $schedule->expects($this->any())
            ->method('setExecutedAt')
            ->will($this->returnSelf());
        $schedule->expects($this->at(6))
            ->method('save');

        // cron end execute some job
        $schedule->expects($this->at(7))
            ->method('setStatus')
            ->with($this->equalTo(Magento_Cron_Model_Schedule::STATUS_SUCCESS))
            ->will($this->returnSelf());

        $schedule->expects($this->at(9))
            ->method('save');

        $collection = $this->_getCollection(array($schedule));

        $this->_config->expects($this->once())
            ->method('getJobs')
            ->will($this->returnValue($jobConfig));

        $this->_mockTime(strtotime('+1 day'));

        $scheduleMock = $this->getMockBuilder('Magento_Cron_Model_Schedule')
            ->disableOriginalConstructor()
            ->setMethods(array('getCollection'))
            ->getMock();
        $scheduleMock->expects($this->any())->method('getCollection')->will($this->returnValue($collection));
        $this->_objectManager->expects($this->at(0))
            ->method('create')
            ->with($this->equalTo('Magento_Cron_Model_Schedule'))
            ->will($this->returnValue($scheduleMock));
        $this->_objectManager->expects($this->at(1))
            ->method('create')
            ->with($this->equalTo('CronJob'))
            ->will($this->returnValue($testCronJob));

        $this->_observer->dispatch('');

        $this->assertInstanceOf('Magento_Cron_Model_Schedule', $testCronJob->getParam());
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
            ->with($this->equalTo(Magento_Cron_Model_Observer::CACHE_KEY_LAST_SCHEDULE_GENERATE_AT))
            ->will($this->returnValue(time() - 10000000));
        $this->_app->expects($this->at(2))
            ->method('loadCache')
            ->with($this->equalTo(Magento_Cron_Model_Observer::CACHE_KEY_LAST_HISTORY_CLEANUP_AT))
            ->will($this->returnValue(time() + 10000000));

        $this->_coreStoreConfig->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue(0));

        $schedule = $this->getMockBuilder('Magento_Cron_Model_Schedule')
            ->setMethods(array('getJobCode', 'getScheduledAt'))
            ->disableOriginalConstructor()
            ->getMock();
        $schedule->expects($this->any())
            ->method('getJobCode')
            ->will($this->returnValue('job_code1'));
        $schedule->expects($this->once())
            ->method('getScheduledAt')
            ->will($this->returnValue('* * * * *'));

        $collection = $this->_getCollection(array(new Magento_Object(), $schedule));

        $this->_app->expects($this->any())->method('saveCache');

        $scheduleMock = $this->getMockBuilder('Magento_Cron_Model_Schedule')
            ->disableOriginalConstructor()
            ->setMethods(array('getCollection'))
            ->getMock();
        $scheduleMock->expects($this->any())->method('getCollection')->will($this->returnValue($collection));
        $this->_objectManager->expects($this->any())
            ->method('create')
            ->with($this->equalTo('Magento_Cron_Model_Schedule'))
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
            ->with($this->equalTo(Magento_Cron_Model_Observer::CACHE_KEY_LAST_SCHEDULE_GENERATE_AT))
            ->will($this->returnValue(time() - 10000000));
        $this->_app->expects($this->at(2))
            ->method('loadCache')
            ->with($this->equalTo(Magento_Cron_Model_Observer::CACHE_KEY_LAST_HISTORY_CLEANUP_AT))
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
            'save'
        );
        $schedule = $this->getMockBuilder('Magento_Cron_Model_Schedule')
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

        $collection = $this->_getCollection(array(new Magento_Object(), $schedule));

        $this->_app->expects($this->any())->method('saveCache');

        $scheduleMock = $this->getMockBuilder('Magento_Cron_Model_Schedule')
            ->disableOriginalConstructor()
            ->setMethods(array('getCollection'))
            ->getMock();
        $scheduleMock->expects($this->any())->method('getCollection')->will($this->returnValue($collection));
        $this->_objectManager->expects($this->any())
            ->method('create')
            ->with($this->equalTo('Magento_Cron_Model_Schedule'))
            ->will($this->returnValue($scheduleMock));

        $this->_observer->dispatch('');
    }

    /**
     * Test case without saved cron jobs in data base
     */
    public function testDispatchCleanup()
    {
        $schedule = $this->getMockBuilder('Magento_Cron_Model_Schedule')
            ->disableOriginalConstructor()
            ->setMethods(array('getExecutedAt', 'getStatus', 'delete'))
            ->getMock();
        $schedule->expects($this->any())->method('getExecutedAt')->will($this->returnValue('-1 day'));
        $schedule->expects($this->any())->method('getStatus')->will($this->returnValue('success'));

        $collection = $this->_getCollection(array($schedule));

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

        $scheduleMock = $this->getMockBuilder('Magento_Cron_Model_Schedule')
            ->disableOriginalConstructor()
            ->setMethods(array('getCollection'))
            ->getMock();
        $scheduleMock->expects($this->any())->method('getCollection')->will($this->returnValue($collection));
        $this->_objectManager->expects($this->at(0))
            ->method('create')
            ->with($this->equalTo('Magento_Cron_Model_Schedule'))
            ->will($this->returnValue($scheduleMock));

        $collection = $this->_getCollection();
        $collection->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(array($schedule)));
        $scheduleMock = $this->getMockBuilder('Magento_Cron_Model_Schedule')
            ->disableOriginalConstructor()
            ->setMethods(array('getCollection'))
            ->getMock();
        $scheduleMock->expects($this->any())->method('getCollection')->will($this->returnValue($collection));
        $this->_objectManager->expects($this->at(1))
            ->method('create')
            ->with($this->equalTo('Magento_Cron_Model_Schedule'))
            ->will($this->returnValue($scheduleMock));

        $this->_observer->dispatch('');
    }

    /**
     * Manipulate with time value, we set big value because we expect that construction
     * if ($lastRun > time() - $schedulePeriod) {
     *     return $this;
     * }
     * in Magento_Cron_Model_Observer::_generate() and in Magento_Cron_Model_Observer::_cleanup() , will return $this;
     *
     */
    protected function _mockTime($val = 0)
    {
        $lastRun = time() + 10000000;
        $this->_app->expects($this->any())
            ->method('loadCache')
            ->will($this->returnValue($lastRun));

        $this->_coreStoreConfig->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($val));
    }

    /**
     * @param array $items
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getCollection($items = array())
    {
        $collection = $this->getMockBuilder('Magento_Cron_Model_Resource_Schedule_Collection')
            ->setMethods(array('addFieldToFilter', 'load'))
            ->disableOriginalConstructor()
            ->getMock();
        $collection->expects($this->any())
            ->method('addFieldToFilter')
            ->will($this->returnSelf());
        $collection->expects($this->any())
            ->method('load')
            ->will($this->returnSelf());

        foreach ($items as $data) {
            $collection->addItem($data);
        }
        return $collection;
    }
}
