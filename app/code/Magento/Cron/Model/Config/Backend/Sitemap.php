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
 * Backend Model for Currency import options
 *
 * @category   Magento
 * @package    Magento_Cron
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Cron\Model\Config\Backend;

class Sitemap extends \Magento\Core\Model\Config\Value
{

    const CRON_STRING_PATH = 'crontab/jobs/sitemap_generate/schedule/cron_expr';
    const CRON_MODEL_PATH = 'crontab/jobs/sitemap_generate/run/model';

    protected function _afterSave()
    {
        $enabled = $this->getData('groups/generate/enabled/value');
        //$service = $this->getData('groups/import/fields/service/value');
        $time = $this->getData('groups/generate/fields/time/value');
        $frequncy = $this->getData('groups/generate/frequency/value');
        $errorEmail = $this->getData('groups/generate/error_email/value');

        $frequencyDaily = \Magento\Cron\Model\Config\Source\Frequency::CRON_DAILY;
        $frequencyWeekly = \Magento\Cron\Model\Config\Source\Frequency::CRON_WEEKLY;
        $frequencyMonthly = \Magento\Cron\Model\Config\Source\Frequency::CRON_MONTHLY;

        $cronDayOfWeek = date('N');

        $cronExprArray = array(
            intval($time[1]),                                   # Minute
            intval($time[0]),                                   # Hour
            ($frequncy == $frequencyMonthly) ? '1' : '*',       # Day of the Month
            '*',                                                # Month of the Year
            ($frequncy == $frequencyWeekly) ? '1' : '*',        # Day of the Week
        );

        $cronExprString = join(' ', $cronExprArray);

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
        } catch (\Exception $e) {
            throw new \Exception(__("We can't save the cron expression."));
        }
    }

}
