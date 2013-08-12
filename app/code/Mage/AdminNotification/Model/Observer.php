<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * AdminNotification observer
 *
 * @category   Mage
 * @package    Mage_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_AdminNotification_Model_Observer
{
    /**
     * Predispath admin action controller
     *
     * @param Magento_Event_Observer $observer
     */
    public function preDispatch(Magento_Event_Observer $observer)
    {

        if (Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isLoggedIn()) {

            $feedModel  = Mage::getModel('Mage_AdminNotification_Model_Feed');
            /* @var $feedModel Mage_AdminNotification_Model_Feed */

            $feedModel->checkUpdate();
        }
    }
}
