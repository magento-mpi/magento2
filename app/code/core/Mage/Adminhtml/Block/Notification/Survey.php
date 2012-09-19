<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml AdminNotification survey question block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Notification_Survey extends Mage_Adminhtml_Block_Template
{
    /**
     * Check whether survey question can show
     *
     * @return boolean
     */
    public function canShow()
    {
        $adminSession = Mage::getSingleton('Mage_Backend_Model_Auth_Session');
        $seconds = intval(date('s', time()));
        if ($adminSession->getHideSurveyQuestion()
            || !Mage::getSingleton('Mage_Core_Model_Authorization')
                ->isAllowed(Mage_Backend_Model_Acl_Config::ACL_RESOURCE_ALL)
            || Mage_AdminNotification_Model_Survey::isSurveyViewed()
            || !Mage_AdminNotification_Model_Survey::isSurveyUrlValid())
        {
            return false;
        }
        return true;
    }

    /**
     * Return survey url
     *
     * @return string
     */
    public function getSurveyUrl()
    {
        return Mage_AdminNotification_Model_Survey::getSurveyUrl();
    }
}
