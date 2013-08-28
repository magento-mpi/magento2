<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Paypal_Model_System_Config_Backend_Cron extends Magento_Core_Model_Config_Data
{
    const CRON_STRING_PATH = 'crontab/jobs/paypal_fetch_settlement_reports/schedule/cron_expr';
    const CRON_MODEL_PATH_INTERVAL = 'paypal/fetch_reports/schedule';

    /**
     * Cron settings after save
     * @return void
     */
    protected function _afterSave()
    {
        $cronExprString = '';
        $time = explode(',', Mage::getModel('Magento_Core_Model_Config_Data')->load('paypal/fetch_reports/time', 'path')->getValue());
        if (Mage::getModel('Magento_Core_Model_Config_Data')->load('paypal/fetch_reports/active', 'path')->getValue()) {
            $interval = Mage::getModel('Magento_Core_Model_Config_Data')->load(self::CRON_MODEL_PATH_INTERVAL, 'path')->getValue();
            $cronExprString = "{$time[1]} {$time[0]} */{$interval} * *";
        }

        Mage::getModel('Magento_Core_Model_Config_Data')
            ->load(self::CRON_STRING_PATH, 'path')
            ->setValue($cronExprString)
            ->setPath(self::CRON_STRING_PATH)
            ->save();

        return parent::_afterSave();
    }
}
