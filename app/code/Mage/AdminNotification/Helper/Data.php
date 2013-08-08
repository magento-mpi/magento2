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
 * AdminNotification Data helper
 *
 * @category   Mage
 * @package    Mage_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_AdminNotification_Helper_Data extends Magento_Core_Helper_Abstract
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
     * @var Mage_AdminNotification_Model_Inbox
     */
    protected $_latestNotice;

    /**
     * count of unread notes by type
     *
     * @var array
     */
    protected $_unreadNoticeCounts;

    /**
     * Retrieve latest notice model
     *
     * @return Mage_AdminNotification_Model_Inbox
     */
    public function getLatestNotice()
    {
        if (is_null($this->_latestNotice)) {
            $this->_latestNotice = Mage::getModel('Mage_AdminNotification_Model_Inbox')->loadLatestNotice();
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
            $this->_unreadNoticeCounts = Mage::getModel('Mage_AdminNotification_Model_Inbox')->getNoticeStatus();
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
            $sheme = Mage::app()->getFrontController()->getRequest()->isSecure()
                ? 'https://'
                : 'http://';

            $this->_popupUrl = $sheme . Mage::getStoreConfig(self::XML_PATH_POPUP_URL);
        }
        return $this->_popupUrl . ($withExt ? '.swf' : '');
    }
}
