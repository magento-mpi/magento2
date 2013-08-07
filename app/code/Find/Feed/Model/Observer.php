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
 * TheFind feed main observer 
 *
 * @category    Find
 * @package     Find_Feed
 */
class Find_Feed_Model_Observer
{
    /**
     * Save system config event 
     *
     * @param Magento_Object $observer
     */
    public function saveSystemConfig($observer)
    {
        $store = $observer->getStore();
        $website = $observer->getWebsite();
        $groups['settings']['fields']['cron_schedule']['value'] = $this->_getSchedule();

        Mage::getModel('Mage_Backend_Model_Config')
                ->setSection('feed')
                ->setWebsite($website)
                ->setStore($store)
                ->setGroups($groups)
                ->save();
    }

    /**
     * Transform system settings option to cron schedule string
     * 
     * @return string
     */
    protected function _getSchedule()
    {
        $data = Mage::app()->getRequest()->getPost('groups');

        $frequency = !empty($data['settings']['fields']['cron_frequency']['value'])?
                         $data['settings']['fields']['cron_frequency']['value']:
                         0;
        $hours     = !empty($data['settings']['fields']['cron_hours']['value'])?
                         $data['settings']['fields']['cron_hours']['value']:
                         0;
        
        $schedule = "0 $hours ";

        switch ($frequency) {
            case Find_Feed_Model_Adminhtml_System_Source_Cron_Frequency::DAILY:
                $schedule .= "* * *"; 
                break;
            case Find_Feed_Model_Adminhtml_System_Source_Cron_Frequency::WEEKLY:
                $schedule .= "* * 1"; 
                break;
            case Find_Feed_Model_Adminhtml_System_Source_Cron_Frequency::MONTHLY:
                $schedule .= "1 * *"; 
                break;
            case Find_Feed_Model_Adminhtml_System_Source_Cron_Frequency::EVERY_MINUTE:
                $schedule = "0-59 * * * *"; 
                break;
            default:
                $schedule .= "* */1 *"; 
                break;
        }

        return $schedule;
    }
}
