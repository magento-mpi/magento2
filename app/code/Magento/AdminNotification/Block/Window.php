<?php
/**
 * Critical notification window
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_AdminNotification_Block_Window extends Magento_Backend_Block_Template
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
     * Authentication
     *
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_authSession;

    /**
     * Critical messages collection
     *
     * @var Magento_AdminNotification_Model_Resource_Inbox_Collection
     */
    protected $_criticalCollection;

    /**
     * @var Magento_Adminnotification_Model_Inbox
     */
    protected $_latestItem;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Backend_Model_Auth_Session $authSession
     * @param Magento_AdminNotification_Model_Resource_Inbox_Collection_Critical $criticalCollection
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Backend_Model_Auth_Session $authSession,
        Magento_AdminNotification_Model_Resource_Inbox_Collection_Critical $criticalCollection,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_authSession = $authSession;
        $this->_criticalCollection = $criticalCollection;
    }

    /**
     * Render block
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->canShow()) {
            $this->setHeaderText($this->escapeHtml(__('Incoming Message')));
            $this->setCloseText($this->escapeHtml(__('close')));
            $this->setReadDetailsText($this->escapeHtml(__('Read details')));
            $this->setNoticeMessageText($this->escapeHtml($this->_getLatestItem()->getTitle()));
            $this->setNoticeMessageUrl($this->escapeUrl($this->_getLatestItem()->getUrl()));
            $this->setSeverityText('critical');
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Retrieve latest critical item
     *
     * @return bool|Magento_Adminnotification_Model_Inbox
     */
    protected function _getLatestItem()
    {
        if ($this->_latestItem == null) {
            $items = array_values($this->_criticalCollection->getItems());
            if (count($items)) {
                $this->_latestItem = $items[0];
            } else {
                $this->_latestItem = false;
            }
        }
        return $this->_latestItem;
    }

    /**
     * Check whether block should be displayed
     *
     * @return bool
     */
    public function canShow()
    {
        return $this->_authSession->isFirstPageAfterLogin() && $this->_getLatestItem();
    }
}
