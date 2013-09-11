<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend model for import/export log cleaning schedule options
 *
 * @category   Magento
 * @package    Magento_ScheduledImportExport
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ScheduledImportExport\Model\System\Config\Backend\Logclean;

class Cron extends \Magento\Core\Model\Config\Value
{
    /**
     * Cron expression configuration path
     */
    const CRON_STRING_PATH = 'crontab/jobs/magento_scheduled_import_export_log_clean/schedule/cron_expr';

    /**
     * Add cron task
     *
     * @return void
     */
    protected function _afterSave()
    {
        $time = $this->getData('groups/magento_scheduled_import_export_log/fields/time/value');
        $frequency = $this->getData('groups/magento_scheduled_import_export_log/fields/frequency/value');

        $frequencyDaily   = \Magento\Cron\Model\Config\Source\Frequency::CRON_DAILY;
        $frequencyWeekly  = \Magento\Cron\Model\Config\Source\Frequency::CRON_WEEKLY;
        $frequencyMonthly = \Magento\Cron\Model\Config\Source\Frequency::CRON_MONTHLY;

        $cronExprArray = array(
            intval($time[1]),                                   # Minute
            intval($time[0]),                                   # Hour
            ($frequency == $frequencyMonthly) ? '1' : '*',      # Day of the Month
            '*',                                                # Month of the Year
            ($frequency == $frequencyWeekly) ? '1' : '*',       # Day of the Week
        );

        $cronExprString = join(' ', $cronExprArray);

        try {
            \Mage::getModel('\Magento\Core\Model\Config\Value')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
        } catch (\Exception $e) {
            throw new \Exception(__('We were unable to save the cron expression.'));
        }
    }

}
