<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_AdminNotification_Block_System_Messages_UnreadMessagePopup extends Mage_Backend_Block_Template
{
    /**
     * List of item classes per severity
     *
     * @var array
     */
    protected $_itemClasses = array(
        Mage_AdminNotification_Model_System_MessageInterface::SEVERITY_CRITICAL => 'error',
        Mage_AdminNotification_Model_System_MessageInterface::SEVERITY_MAJOR => 'warning'
    );

    /**
     * System Message list
     *
     * @var Mage_AdminNotification_Model_Resource_System_Message_Collection
     */
    protected $_messages;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_AdminNotification_Model_Resource_System_Message_Collection_Synchronized $messages
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_AdminNotification_Model_Resource_System_Message_Collection_Synchronized $messages,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_messages = $messages;
    }

    /**
     * Render block
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (count($this->_messages->getUnread())) {
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
        return $this->_messages->getUnread();
    }

    /**
     * Retrieve popup title
     *
     * @return string
     */
    public function getPopupTitle()
    {
        $messageCount = count($this->_messages->getUnread());
        if ($messageCount > 1) {
            return $this->__('You have %1 new system messages', $messageCount);
        } else {
            return $this->__('You have %1 new system message', $messageCount);
        }
    }

    /**
     * Retrieve item class by severity
     *
     * @param Mage_AdminNotification_Model_System_MessageInterface $message
     * @return mixed
     */
    public function getItemClass(Mage_AdminNotification_Model_System_MessageInterface $message)
    {
        return $this->_itemClasses[$message->getSeverity()];
    }
}
