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
 * AdminNotification Data helper
 *
 * @category   Magento
 * @package    Magento_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdminNotification\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    const XML_PATH_POPUP_URL    = 'system/adminnotification/popup_url';

    /**
     * Widget Popup Notification Object URL
     *
     * @var string
     */
    protected $_popupUrl;

    /**
     * Is readable Popup Notification Object flag
     *
     * @var bool
     */
    protected $_popupReadable;

    /**
     * Last Notice object
     *
     * @var \Magento\AdminNotification\Model\Inbox
     */
    protected $_latestNotice;

    /**
     * count of unread notes by type
     *
     * @var array
     */
    protected $_unreadNoticeCounts;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context);
    }

    /**
     * Retrieve latest notice model
     *
     * @return \Magento\AdminNotification\Model\Inbox
     */
    public function getLatestNotice()
    {
        if (is_null($this->_latestNotice)) {
            $this->_latestNotice = \Mage::getModel('Magento\AdminNotification\Model\Inbox')->loadLatestNotice();
        }
        return $this->_latestNotice;
    }

    /**
     * Retrieve count of unread notes by type
     *
     * @param int $severity
     * @return int
     */
    public function getUnreadNoticeCount($severity)
    {
        if (is_null($this->_unreadNoticeCounts)) {
            $this->_unreadNoticeCounts = \Mage::getModel('Magento\AdminNotification\Model\Inbox')->getNoticeStatus();
        }
        return isset($this->_unreadNoticeCounts[$severity]) ? $this->_unreadNoticeCounts[$severity] : 0;
    }

    /**
     * Retrieve Widget Popup Notification Object URL
     *
     * @param bool $withExt
     * @return string
     */
    public function getPopupObjectUrl($withExt = false)
    {
        if (is_null($this->_popupUrl)) {
            $scheme = \Mage::app()->getFrontController()->getRequest()->isSecure()
                ? 'https://'
                : 'http://';

            $this->_popupUrl = $scheme . $this->_coreStoreConfig->getConfig(self::XML_PATH_POPUP_URL);
        }
        return $this->_popupUrl . ($withExt ? '.swf' : '');
    }
}
