<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Backend_Model_System_Message_CacheOutdated extends Magento_AdminNotification_Model_System_Message_CacheOutdated
{
    /**
     * Check whether
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return false;
    }
}
