<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Backup by cron backend model
 *
 * @category   Magento
 * @package    \Magento\Backup
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backup\Model\Config\Backend;

class Cron extends \Magento\Core\Model\Config\Value
{
    const CRON_STRING_PATH  = 'crontab/jobs/system_backup/schedule/cron_expr';
    const CRON_MODEL_PATH   = 'crontab/jobs/system_backup/run/model';

    const XML_PATH_BACKUP_ENABLED       = 'groups/backup/fields/enabled/value';
    const XML_PATH_BACKUP_TIME          = 'groups/backup/fields/time/value';
    const XML_PATH_BACKUP_FREQUENCY     = 'groups/backup/fields/frequency/value';

    /**
     * Cron settings after save
     *
     * @return \Magento\Backend\Model\Config\Backend\Log\Cron
     */
    protected function _afterSave()
    {
        $enabled   = $this->getData(self::XML_PATH_BACKUP_ENABLED);
        $time      = $this->getData(self::XML_PATH_BACKUP_TIME);
        $frequency = $this->getData(self::XML_PATH_BACKUP_FREQUENCY);

        $frequencyWeekly  = \Magento\Cron\Model\Config\Source\Frequency::CRON_WEEKLY;
        $frequencyMonthly = \Magento\Cron\Model\Config\Source\Frequency::CRON_MONTHLY;

        if ($enabled) {
            $cronExprArray = array(
                intval($time[1]),                                   # Minute
                intval($time[0]),                                   # Hour
                ($frequency == $frequencyMonthly) ? '1' : '*',      # Day of the Month
                '*',                                                # Month of the Year
                ($frequency == $frequencyWeekly) ? '1' : '*',       # Day of the Week
            );
            $cronExprString = join(' ', $cronExprArray);
        }
        else {
            $cronExprString = '';
        }

        try {
            \Mage::getModel('\Magento\Core\Model\Config\Value')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();

            \Mage::getModel('\Magento\Core\Model\Config\Value')
                ->load(self::CRON_MODEL_PATH, 'path')
                ->setValue((string) \Mage::getConfig()->getNode(self::CRON_MODEL_PATH))
                ->setPath(self::CRON_MODEL_PATH)
                ->save();
        }
        catch (\Exception $e) {
            \Mage::throwException(__('We can\'t save the Cron expression.'));
        }
    }
}
