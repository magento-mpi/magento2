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
 * Backend Model for product alerts
 *
 * @category   Magento
 * @package    Magento_Cron
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cron_Model_Config_Backend_Product_Alert extends Magento_Core_Model_Config_Value
{
    const CRON_STRING_PATH  = 'crontab/jobs/catalog_product_alert/schedule/cron_expr';
    const CRON_MODEL_PATH   = 'crontab/jobs/catalog_product_alert/run/model';

    protected function _afterSave()
    {
        $priceEnable = $this->getData('groups/productalert/fields/allow_price/value');
        $stockEnable = $this->getData('groups/productalert/fields/allow_stock/value');

        $enabled     = $priceEnable || $stockEnable;
        $frequncy    = $this->getData('groups/productalert_cron/fields/frequency/value');
        $time        = $this->getData('groups/productalert_cron/fields/time/value');

        $errorEmail  = $this->getData('groups/productalert_cron/fields/error_email/value');

        $frequencyDaily     = Magento_Cron_Model_Config_Source_Frequency::CRON_DAILY;
        $frequencyWeekly    = Magento_Cron_Model_Config_Source_Frequency::CRON_WEEKLY;
        $frequencyMonthly   = Magento_Cron_Model_Config_Source_Frequency::CRON_MONTHLY;
        $cronDayOfWeek      = date('N');

        $cronExprArray      = array(
            intval($time[1]),                                   # Minute
            intval($time[0]),                                   # Hour
            ($frequncy == $frequencyMonthly) ? '1' : '*',       # Day of the Month
            '*',                                                # Month of the Year
            ($frequncy == $frequencyWeekly) ? '1' : '*',         # Day of the Week
        );

        $cronExprString     = join(' ', $cronExprArray);

        try {
            Mage::getModel('Magento_Core_Model_Config_Value')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
            Mage::getModel('Magento_Core_Model_Config_Value')
                ->load(self::CRON_MODEL_PATH, 'path')
                ->setValue((string) $this->_coreConfig->getNode(self::CRON_MODEL_PATH))
                ->setPath(self::CRON_MODEL_PATH)
                ->save();
        } catch (Exception $e) {
            throw new Exception(__('We can\'t save the Cron expression.'));
        }
    }
}
