<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Find
 * @package     Find_Feed
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
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
     * @param Varien_Object $observer
     */
    public function saveSystemConfig($observer)
    {
        $store = $observer->getStore();
        $website = $observer->getWebsite();
        $groups['settings']['fields']['cron_schedule']['value'] = $this->_getSchedule();

        Mage::getModel('adminhtml/config_data')
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
