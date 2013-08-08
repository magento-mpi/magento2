<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Cron_Model_Config_Source_Frequency implements Magento_Core_Model_Option_ArrayInterface
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
                    'label' => Mage::helper('Mage_Cron_Helper_Data')->__('Daily'),
                    'value' => self::CRON_DAILY,
                ),
                array(
                    'label' => Mage::helper('Mage_Cron_Helper_Data')->__('Weekly'),
                    'value' => self::CRON_WEEKLY,
                ),
                array(
                    'label' => Mage::helper('Mage_Cron_Helper_Data')->__('Monthly'),
                    'value' => self::CRON_MONTHLY,
                ),
            );
        }
        return self::$_options;
    }

}
