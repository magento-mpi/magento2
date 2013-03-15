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
 * Source for cron hours
 *
 * @category    Find
 * @package     Find_Feed
 */
class Find_Feed_Model_Adminhtml_System_Source_Cron_Hours
{

    /**
     * Fetch options array
     * 
     * @return array
     */
    public function toOptionArray()
    {
        $hours = array();
        for ($i = 1; $i <= 24; $i++) {
            $hours[] = array('label' => $i, 'value' => $i);
        }
        return $hours;
    }
}
