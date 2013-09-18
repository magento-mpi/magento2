<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reminder Cron Backend Model
 */
namespace Magento\Reminder\Model\System\Config\Backend;

class Cron extends \Magento\Core\Model\Config\Value
{
    const CRON_STRING_PATH  = 'crontab/jobs/send_notification/schedule/cron_expr';
    const CRON_MODEL_PATH   = 'crontab/jobs/send_notification/run/model';

    /**
     * Cron settings after save
     *
     * @return \Magento\Reminder\Model\System\Config\Backend\Cron
     */
    protected function _afterSave()
    {
        $cronExprString = '';

        if ($this->getFieldsetDataValue('enabled')) {
            $minutely = \Magento\Reminder\Model\Observer::CRON_MINUTELY;
            $hourly   = \Magento\Reminder\Model\Observer::CRON_HOURLY;
            $daily    = \Magento\Reminder\Model\Observer::CRON_DAILY;

            $frequency  = $this->getFieldsetDataValue('frequency');

            if ($frequency == $minutely) {
                $interval = (int)$this->getFieldsetDataValue('interval');
                $cronExprString = "*/{$interval} * * * *";
            }
            elseif ($frequency == $hourly) {
                $minutes = (int)$this->getFieldsetDataValue('minutes');
                if ($minutes >= 0 && $minutes <= 59){
                    $cronExprString = "{$minutes} * * * *";
                }
                else {
                    \Mage::throwException(__('Please specify a valid number of minute.'));
                }
            }
            elseif ($frequency == $daily) {
                $time = $this->getFieldsetDataValue('time');
                $timeMinutes = intval($time[1]);
                $timeHours = intval($time[0]);
                $cronExprString = "{$timeMinutes} {$timeHours} * * *";
            }
        }

        try {
            \Mage::getModel('Magento\Core\Model\Config\Value')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();

            \Mage::getModel('Magento\Core\Model\Config\Value')
                ->load(self::CRON_MODEL_PATH, 'path')
                ->setValue((string) $this->_coreConfig->getNode(self::CRON_MODEL_PATH))
                ->setPath(self::CRON_MODEL_PATH)
                ->save();
        }

        catch (\Exception $e) {
            \Mage::throwException(__('Unable to save Cron expression'));
        }
    }
}
