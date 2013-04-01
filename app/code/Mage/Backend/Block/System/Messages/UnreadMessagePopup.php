<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Backend_Block_System_Messages_UnreadMessagePopup extends Mage_Backend_Block_Template
{
    /**
     * System Message list
     *
     * @var Mage_Backend_Model_System_MessagingService
     */
    protected $_messagingService;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Backend_Model_System_MessagingService $messagingService
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Backend_Model_System_MessagingService $messagingService,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_messagingService = $messagingService;
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (count($this->_messagingService->hasUnread())) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Retrieve list of unread messages
     *
     * @return mixed
     */
    public function getUnreadMessages()
    {
        return $this->_messagingService->getUnread();
    }
}
