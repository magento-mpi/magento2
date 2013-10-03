<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Cron\Model\Config\Source;

class Frequency implements \Magento\Core\Model\Option\ArrayInterface
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
