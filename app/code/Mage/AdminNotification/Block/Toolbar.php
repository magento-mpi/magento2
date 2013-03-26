<?php
/**
 * Adminhtml AdminNotification toolbar
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_AdminNotification_Block_Adminhtml_Toolbar extends Mage_Backend_Block_Template
{
    /**
     * Retrieve helper
     *
     * @return Mage_AdminNotification_Helper_Data
     */
    protected function _getHelper()
    {
        return $this->_helperFactory->get('Mage_AdminNotification_Helper_Data');
    }

    /**
     * Check is show toolbar
     *
     * @return bool
     */
    public function isShow()
    {
        if (!$this->isOutputEnabled('Mage_AdminNotification')) {
            return false;
        }
        if ($this->getRequest()->getControllerName() == 'notification') {
            return false;
        }
        if ($this->getCriticalCount() == 0 && $this->getMajorCount() == 0 && $this->getMinorCount() == 0
            && $this->getNoticeCount() == 0
        ) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve count of critical errors
     *
     * @return int
     */
    public function getCriticalCount()
    {
        return $this->_getHelper()->getUnreadNoticeCount(Mage_AdminNotification_Model_Inbox::SEVERITY_CRITICAL);
    }

    /**
     * Retrieve count of major errors
     *
     * @return int
     */
    public function getMajorCount()
    {
        return $this->_getHelper()->getUnreadNoticeCount(Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR);
    }

    /**
     * Retrieve count of minor errors
     *
     * @return int
     */
    public function getMinorCount()
    {
        return $this->_getHelper()->getUnreadNoticeCount(Mage_AdminNotification_Model_Inbox::SEVERITY_MINOR);
    }

    /**
     * Retrieve count of notices
     *
     * @return int
     */
    public function getNoticeCount()
    {
        return $this->_getHelper()->getUnreadNoticeCount(Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE);
    }

    /**
     * Retrieve Notices Inbox URL
     *
     * @return string
     */
    public function getNoticesInboxUrl()
    {
        return $this->getUrl('adminhtml/notification');
    }

    /**
     * Retrieve last notice Title
     *
     * @return string
     */
    public function getLatestNotice()
    {
        return  $this->_getHelper()->getLatestNotice()->getTitle();
    }

    /**
     * Retrieve Last Notice URL
     *
     * @return string
     */
    public function getLatestNoticeUrl()
    {
        return $this->_getHelper()->getLatestNotice()->getUrl();
    }

    /**
     * Check is Message Window Available
     *
     * @return bool
     */
    public function isMessageWindowAvailable()
    {
        $block = $this->getLayout()->getBlock('notification_window');
        if ($block) {
            return $block->canShow();
        }
        return false;
    }
}
