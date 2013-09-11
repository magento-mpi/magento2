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
 * Crontab observer
 *
 * @category    Magento
 * @package     Magento_Cron
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Cron\Model;

class Observer
{
    const CACHE_KEY_LAST_SCHEDULE_GENERATE_AT   = 'cron_last_schedule_generate_at';
    const CACHE_KEY_LAST_HISTORY_CLEANUP_AT     = 'cron_last_history_cleanup_at';

    const XML_PATH_SCHEDULE_GENERATE_EVERY  = 'system/cron/schedule_generate_every';
    const XML_PATH_SCHEDULE_AHEAD_FOR       = 'system/cron/schedule_ahead_for';
    const XML_PATH_SCHEDULE_LIFETIME        = 'system/cron/schedule_lifetime';
    const XML_PATH_HISTORY_CLEANUP_EVERY    = 'system/cron/history_cleanup_every';
    const XML_PATH_HISTORY_SUCCESS          = 'system/cron/history_success_lifetime';
    const XML_PATH_HISTORY_FAILURE          = 'system/cron/history_failure_lifetime';

    const REGEX_RUN_MODEL = '#^([a-z0-9_]+)::([a-z0-9_]+)$#i';

    protected $_pendingSchedules;

    /**
     * Process cron queue
     * Geterate tasks schedule
     * Cleanup tasks schedule
     *
     * @param \Magento\Event\Observer $observer
     */
    public function dispatch($observer)
    {
        $schedules = $this->getPendingSchedules();
        $scheduleLifetime = \Mage::getStoreConfig(self::XML_PATH_SCHEDULE_LIFETIME) * 60;
        $now = time();
        $jobsRoot = \Mage::getConfig()->getNode('crontab/jobs');
        $defaultJobsRoot = \Mage::getConfig()->getValue('crontab/jobs', 'default');

        foreach ($schedules->getIterator() as $schedule) {
            $jobConfig = $jobsRoot->{$schedule->getJobCode()};
            if (!$jobConfig || !$jobConfig->run) {
                $jobConfig = isset($defaultJobsRoot[$schedule->getJobCode()])
                    ? $defaultJobsRoot[$schedule->getJobCode()]
                    : false;
                if (!$jobConfig || !$jobConfig['run']) {
                    continue;
                }
            }

            $runConfig = $jobConfig->run;
            $time = strtotime($schedule->getScheduledAt());
            if ($time > $now) {
                continue;
            }
            try {
                $errorStatus = \Magento\Cron\Model\Schedule::STATUS_ERROR;
                $errorMessage = __('Sorry, something went wrong.');

                if ($time < $now - $scheduleLifetime) {
                    $errorStatus = \Magento\Cron\Model\Schedule::STATUS_MISSED;
                    \Mage::throwException(__('Too late for the schedule'));
                }

                if ($runConfig->model) {
                    if (!preg_match(self::REGEX_RUN_MODEL, (string)$runConfig->model, $run)) {
                        \Mage::throwException(__('Invalid model/method definition, expecting "Model_Class::method".'));
                    }
                    if (!($model = \Mage::getModel($run[1])) || !method_exists($model, $run[2])) {
                        \Mage::throwException(__('Invalid callback: %1::%2 does not exist', $run[1], $run[2]));
                    }
                    $callback = array($model, $run[2]);
                    $arguments = array($schedule);
                }
                if (empty($callback)) {
                    \Mage::throwException(__('No callbacks found'));
                }

                if (!$schedule->tryLockJob()) {
                    // another cron started this job intermittently, so skip it
                    continue;
                }
                /**
                    though running status is set in tryLockJob we must set it here because the object
                    was loaded with a pending status and will set it back to pending if we don't set it here
                 */
                $schedule
                    ->setStatus(\Magento\Cron\Model\Schedule::STATUS_RUNNING)
                    ->setExecutedAt(strftime('%Y-%m-%d %H:%M:%S', time()))
                    ->save();

                call_user_func_array($callback, $arguments);

                $schedule
                    ->setStatus(\Magento\Cron\Model\Schedule::STATUS_SUCCESS)
                    ->setFinishedAt(strftime('%Y-%m-%d %H:%M:%S', time()));

            } catch (\Exception $e) {
                $schedule->setStatus($errorStatus)
                    ->setMessages($e->__toString());
            }
            $schedule->save();
        }

        $this->generate();
        $this->cleanup();
    }

    public function getPendingSchedules()
    {
        if (!$this->_pendingSchedules) {
            $this->_pendingSchedules = \Mage::getModel('Magento\Cron\Model\Schedule')->getCollection()
                ->addFieldToFilter('status', \Magento\Cron\Model\Schedule::STATUS_PENDING)
                ->load();
        }
        return $this->_pendingSchedules;
    }

    /**
     * Generate cron schedule
     *
     * @return \Magento\Cron\Model\Observer
     */
    public function generate()
    {
        /**
         * check if schedule generation is needed
         */
        $lastRun = \Mage::app()->loadCache(self::CACHE_KEY_LAST_SCHEDULE_GENERATE_AT);
        if ($lastRun > time() - \Mage::getStoreConfig(self::XML_PATH_SCHEDULE_GENERATE_EVERY)*60) {
            return $this;
        }

        $schedules = $this->getPendingSchedules();
        $exists = array();
        foreach ($schedules->getIterator() as $schedule) {
            $exists[$schedule->getJobCode().'/'.$schedule->getScheduledAt()] = 1;
        }

        /**
         * generate global crontab jobs
         */
        $config = \Mage::getConfig()->getNode('crontab/jobs');
        if ($config instanceof \Magento\Core\Model\Config\Element) {
            $this->_generateJobs($config->asArray(), $exists);
        }

        /**
         * generate configurable crontab jobs
         */
        $config = \Mage::getConfig()->getValue('crontab/jobs', 'default');
        if ($config) {
            $this->_generateJobs($config, $exists);
        }

        /**
         * save time schedules generation was ran with no expiration
         */
        \Mage::app()->saveCache(time(), self::CACHE_KEY_LAST_SCHEDULE_GENERATE_AT, array('crontab'), null);

        return $this;
    }

    /**
     * Generate jobs for config information
     *
     * @param   $jobs
     * @param   array $exists
     * @return  \Magento\Cron\Model\Observer
     */
    protected function _generateJobs($jobs, $exists)
    {
        $scheduleAheadFor = \Mage::getStoreConfig(self::XML_PATH_SCHEDULE_AHEAD_FOR)*60;
        $schedule = \Mage::getModel('Magento\Cron\Model\Schedule');

        foreach ($jobs as $jobCode => $jobConfig) {
            $cronExpr = null;
            if (isset($jobConfig['schedule']['config_path'])) {
                $cronExpr = \Mage::getStoreConfig($jobConfig['schedule']['config_path']);
            }
            if (empty($cronExpr) && isset($jobConfig['schedule']['cron_expr'])) {
                $cronExpr = $jobConfig['schedule']['cron_expr'];
            }
            if (!$cronExpr) {
                continue;
            }

            $now = time();
            $timeAhead = $now + $scheduleAheadFor;
            $schedule->setJobCode($jobCode)
                ->setCronExpr($cronExpr)
                ->setStatus(\Magento\Cron\Model\Schedule::STATUS_PENDING);

            for ($time = $now; $time < $timeAhead; $time += 60) {
                $ts = strftime('%Y-%m-%d %H:%M:00', $time);
                if (!empty($exists[$jobCode.'/'.$ts])) {
                    // already scheduled
                    continue;
                }
                if (!$schedule->trySchedule($time)) {
                    // time does not match cron expression
                    continue;
                }
                $schedule->unsScheduleId()->save();
            }
        }
        return $this;
    }

    public function cleanup()
    {
        // check if history cleanup is needed
        $lastCleanup = \Mage::app()->loadCache(self::CACHE_KEY_LAST_HISTORY_CLEANUP_AT);
        if ($lastCleanup > time() - \Mage::getStoreConfig(self::XML_PATH_HISTORY_CLEANUP_EVERY)*60) {
            return $this;
        }

        $history = \Mage::getModel('Magento\Cron\Model\Schedule')->getCollection()
            ->addFieldToFilter('status', array('in'=>array(
                \Magento\Cron\Model\Schedule::STATUS_SUCCESS,
                \Magento\Cron\Model\Schedule::STATUS_MISSED,
                \Magento\Cron\Model\Schedule::STATUS_ERROR,
            )))->load();

        $historyLifetimes = array(
            \Magento\Cron\Model\Schedule::STATUS_SUCCESS => \Mage::getStoreConfig(self::XML_PATH_HISTORY_SUCCESS)*60,
            \Magento\Cron\Model\Schedule::STATUS_MISSED => \Mage::getStoreConfig(self::XML_PATH_HISTORY_FAILURE)*60,
            \Magento\Cron\Model\Schedule::STATUS_ERROR => \Mage::getStoreConfig(self::XML_PATH_HISTORY_FAILURE)*60,
        );

        $now = time();
        foreach ($history->getIterator() as $record) {
            if (strtotime($record->getExecutedAt()) < $now-$historyLifetimes[$record->getStatus()]) {
                $record->delete();
            }
        }

        // save time history cleanup was ran with no expiration
        \Mage::app()->saveCache(time(), self::CACHE_KEY_LAST_HISTORY_CLEANUP_AT, array('crontab'), null);

        return $this;
    }
}
