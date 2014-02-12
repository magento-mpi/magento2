<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\AdminNotification\Block\System\Messages;

class UnreadMessagePopup extends \Magento\Backend\Block\Template
{
    /**
     * List of item classes per severity
     *
     * @var array
     */
    protected $_itemClasses = array(
        \Magento\AdminNotification\Model\System\MessageInterface::SEVERITY_CRITICAL => 'error',
        \Magento\AdminNotification\Model\System\MessageInterface::SEVERITY_MAJOR => 'warning'
    );

    /**
     * System Message list
     *
     * @var \Magento\AdminNotification\Model\Resource\System\Message\Collection
     */
    protected $_messages;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\AdminNotification\Model\Resource\System\Message\Collection\Synchronized $messages
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\AdminNotification\Model\Resource\System\Message\Collection\Synchronized $messages,
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
            return __('You have %1 new system messages', $messageCount);
        } else {
            return __('You have %1 new system message', $messageCount);
        }
    }

    /**
     * Retrieve item class by severity
     *
     * @param \Magento\AdminNotification\Model\System\MessageInterface $message
     * @return mixed
     */
    public function getItemClass(\Magento\AdminNotification\Model\System\MessageInterface $message)
    {
        return $this->_itemClasses[$message->getSeverity()];
    }
}
