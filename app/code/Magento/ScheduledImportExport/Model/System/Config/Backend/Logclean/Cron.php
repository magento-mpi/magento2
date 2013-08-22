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
class Magento_ScheduledImportExport_Model_System_Config_Backend_Logclean_Cron extends Magento_Core_Model_Config_Data
{
    /**
     * Cron expression configuration path
     */
    const CRON_STRING_PATH = 'crontab/jobs/enterprise_import_export_log_clean/schedule/cron_expr';

    /**
     * Add cron task
     *
     * @return void
     */
    protected function _afterSave()
    {
        $time = $this->getData('groups/enterprise_import_export_log/fields/time/value');
        $frequency = $this->getData('groups/enterprise_import_export_log/fields/frequency/value');

        $frequencyDaily   = Magento_Cron_Model_Config_Source_Frequency::CRON_DAILY;
        $frequencyWeekly  = Magento_Cron_Model_Config_Source_Frequency::CRON_WEEKLY;
        $frequencyMonthly = Magento_Cron_Model_Config_Source_Frequency::CRON_MONTHLY;

        $cronExprArray = array(
            intval($time[1]),                                   # Minute
            intval($time[0]),                                   # Hour
            ($frequency == $frequencyMonthly) ? '1' : '*',      # Day of the Month
            '*',                                                # Month of the Year
            ($frequency == $frequencyWeekly) ? '1' : '*',       # Day of the Week
        );

        $cronExprString = join(' ', $cronExprArray);

        try {
            Mage::getModel('Magento_Core_Model_Config_Data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
        } catch (Exception $e) {
            throw new Exception(__('We were unable to save the cron expression.'));
        }
    }

}
