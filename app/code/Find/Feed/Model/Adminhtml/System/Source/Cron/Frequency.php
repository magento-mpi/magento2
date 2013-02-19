<?php
/**
 * {license_notice}
 *
 * @category    
 * @package     _home
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Source for cron frequency 
 *
 * @category    Find
 * @package     Find_Feed
 */
class Find_Feed_Model_Adminhtml_System_Source_Cron_Frequency
{
    const DAILY   = 1;
    const WEEKLY  = 2;
    const MONTHLY = 3;
    const EVERY_MINUTE = 4;

    /**
     * Fetch options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
               'label' => 'Daily',
               'value' => self::DAILY),
            array(
               'label' => 'Weekly',
               'value' => self::WEEKLY),
            array(
                'label' => 'Monthly',
                'value' => self::MONTHLY),
            array(
                'label' => 'Every minute',
                'value' => self::EVERY_MINUTE)
        );
    }
}
