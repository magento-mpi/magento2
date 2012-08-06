<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Notification_Window extends Mage_Adminhtml_Block_Notification_Toolbar
{
    /**
     * XML path of Severity icons url
     */
    const XML_SEVERITY_ICONS_URL_PATH  = 'system/adminnotification/severity_icons_url';

    /**
     * Severity icons url
     *
     * @var string
     */
    protected $_severityIconsUrl;

    /**
     * Is available flag
     *
     * @var bool
     */
    protected $_available = null;

    /**
     * Initialize block window
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setHeaderText($this->escapeHtml($this->__('Incoming Message')));
        $this->setCloseText($this->escapeHtml($this->__('close')));
        $this->setReadDetailsText($this->escapeHtml($this->__('Read details')));
        $this->setNoticeText($this->escapeHtml($this->__('NOTICE')));
        $this->setMinorText($this->escapeHtml($this->__('MINOR')));
        $this->setMajorText($this->escapeHtml($this->__('MAJOR')));
        $this->setCriticalText($this->escapeHtml($this->__('CRITICAL')));


        $this->setNoticeMessageText($this->escapeHtml($this->getLastNotice()->getTitle()));
        $this->setNoticeMessageUrl($this->escapeUrl($this->getLastNotice()->getUrl()));

        switch ($this->getLastNotice()->getSeverity()) {
            default:
            case Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE:
                $severity = 'SEVERITY_NOTICE';
                break;
            case Mage_AdminNotification_Model_Inbox::SEVERITY_MINOR:
                $severity = 'SEVERITY_MINOR';
                break;
            case Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR:
                $severity = 'SEVERITY_MAJOR';
                break;
            case Mage_AdminNotification_Model_Inbox::SEVERITY_CRITICAL:
                $severity = 'SEVERITY_CRITICAL';
                break;
        }

        $this->setNoticeSeverity($severity);
    }

    /**
     * Can we show notification window
     *
     * @return bool
     */
    public function canShow()
    {
        if (!is_null($this->_available)) {
            return $this->_available;
        }

        if (!Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isFirstPageAfterLogin()) {
            $this->_available = false;
            return false;
        }

        if (!$this->isOutputEnabled('Mage_AdminNotification')) {
            $this->_available = false;
            return false;
        }

        if (!$this->_isAllowed()) {
            $this->_available = false;
            return false;
        }

        if (is_null($this->_available)) {
            $this->_available = $this->isShow();
        }
        return $this->_available;
    }


    /**
     * Return swf object url
     *
     * @return string
     */
    public function getObjectUrl()
    {
        return $this->_getHelper()->getPopupObjectUrl();
    }

    /**
     * Retrieve Last Notice object
     *
     * @return Mage_AdminNotification_Model_Inbox
     */
    public function getLastNotice()
    {
        return $this->_getHelper()->getLatestNotice();
    }

    /**
     * Retrieve severity icons url
     *
     * @return string
     */
    public function getSeverityIconsUrl()
    {
        if (is_null($this->_severityIconsUrl)) {
            $this->_severityIconsUrl =
                (Mage::app()->getFrontController()->getRequest()->isSecure() ? 'https://' : 'http://')
                . sprintf(Mage::getStoreConfig(self::XML_SEVERITY_ICONS_URL_PATH), Mage::getVersion(),
                    $this->getNoticeSeverity())
            ;
        }
        return $this->_severityIconsUrl;
    }

    /**
     * Retrieve severity text
     *
     * @return string
     */
    public function getSeverityText()
    {
        return strtolower(str_replace('SEVERITY_', '', $this->getNoticeSeverity()));
    }

    /**
     * Check if current block allowed in ACL
     *
     * @param string $resourcePath
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Core_Model_Authorization')
            ->isAllowed('Mage_AdminNotification::show_toolbar');
    }
}
