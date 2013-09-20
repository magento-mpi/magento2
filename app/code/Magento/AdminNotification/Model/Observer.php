<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * AdminNotification observer
 *
 * @category   Magento
 * @package    Magento_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdminNotification_Model_Observer
{
    /**
     * @var Magento_AdminNotification_Model_FeedFactory
     */
    protected $_feedFactory;

    /**
     * @param Magento_AdminNotification_Model_FeedFactory $feedFactory
     */
    public function __construct(Magento_AdminNotification_Model_FeedFactory $feedFactory)
    {
        $this->_feedFactory = $feedFactory;
    }

    /**
     * Predispath admin action controller
     *
     * @param Magento_Event_Observer $observer
     */
    public function preDispatch(Magento_Event_Observer $observer)
    {
        if (Mage::getSingleton('Magento_Backend_Model_Auth_Session')->isLoggedIn()) {
            $feedModel  = $this->_feedFactory->create();
            /* @var $feedModel Magento_AdminNotification_Model_Feed */
            $feedModel->checkUpdate();
        }
    }
}
