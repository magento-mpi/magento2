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
namespace Magento\AdminNotification\Model;

class Observer
{
    /**
     * @var \Magento\AdminNotification\Model\FeedFactory
     */
    protected $_feedFactory;

    /**
     * @param \Magento\AdminNotification\Model\FeedFactory $feedFactory
     */
    public function __construct(\Magento\AdminNotification\Model\FeedFactory $feedFactory)
    {
        $this->_feedFactory = $feedFactory;
    }

    /**
     * Predispath admin action controller
     *
     * @param \Magento\Event\Observer $observer
     */
    public function preDispatch(\Magento\Event\Observer $observer)
    {
        if (\Mage::getSingleton('Magento\Backend\Model\Auth\Session')->isLoggedIn()) {
            $feedModel  = $this->_feedFactory->create();
            /* @var $feedModel \Magento\AdminNotification\Model\Feed */
            $feedModel->checkUpdate();
        }
    }
}
