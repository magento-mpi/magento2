<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Reminder Cron Backend Model
 */
class Enterprise_Reminder_Model_System_Config_Backend_Cron extends Mage_Core_Model_Config_Data
{
    const CRON_STRING_PATH  = 'crontab/jobs/send_notification/schedule/cron_expr';
    const CRON_MODEL_PATH   = 'crontab/jobs/send_notification/run/model';

    /**
     * Cron settings after save
     *
     * @return Enterprise_Reminder_Model_System_Config_Backend_Cron
     */
    protected function _afterSave()
    {
        $enabled     = $this->getData('groups/enterprise_reminder/fields/enabled/value');
        $time        = $this->getData('groups/enterprise_reminder/fields/time/value');
        $frequency   = $this->getData('groups/enterprise_reminder/fields/frequency/value');
        $periodicity = (int)$this->getData('groups/enterprise_reminder/fields/periodicity/value');

        $minutly = Enterprise_Reminder_Model_Observer::CRON_MINUTLY;
        $hourly  = Enterprise_Reminder_Model_Observer::CRON_HOURLY;
        $weekly  = Enterprise_Reminder_Model_Observer::CRON_WEEKLY;
        $monthly = Enterprise_Reminder_Model_Observer::CRON_MONTHLY;

        if ($enabled) {
            if (($frequency == $minutly) && $periodicity < 60) {
                $cronExprString = "*/{$periodicity} * * * *";
            }
            elseif ($frequency == $hourly) {
                $cronExprString = '1 * * * *';
            }
            else {
                $cronExprArray = array(
                    intval($time[1]),                      # Minute
                    intval($time[0]),                      # Hour
                    ($frequency == $monthly) ? '1' : '*',  # Day of the Month
                    '*',                                   # Month of the Year
                    ($frequency == $weekly) ? '1' : '*',   # Day of the Week
                );
                $cronExprString = join(' ', $cronExprArray);
            }
        }
        else {
            $cronExprString = '';
        }

        try {
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();

            Mage::getModel('core/config_data')
                ->load(self::CRON_MODEL_PATH, 'path')
                ->setValue((string) Mage::getConfig()->getNode(self::CRON_MODEL_PATH))
                ->setPath(self::CRON_MODEL_PATH)
                ->save();
        }

        catch (Exception $e) {
            Mage::throwException(Mage::helper('adminhtml')->__('Unable to save Cron expression'));
        }
    }
}
