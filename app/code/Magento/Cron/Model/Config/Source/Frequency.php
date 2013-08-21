<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Cron_Model_Config_Source_Frequency implements Magento_Core_Model_Option_ArrayInterface
{

    protected static $_options;

    const CRON_DAILY    = 'D';
    const CRON_WEEKLY   = 'W';
    const CRON_MONTHLY  = 'M';

    public function toOptionArray()
    {
        if (!self::$_options) {
            self::$_options = array(
                array(
                    'label' => __('Daily'),
                    'value' => self::CRON_DAILY,
                ),
                array(
                    'label' => __('Weekly'),
                    'value' => self::CRON_WEEKLY,
                ),
                array(
                    'label' => __('Monthly'),
                    'value' => self::CRON_MONTHLY,
                ),
            );
        }
        return self::$_options;
    }

}
