<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Magento_AdminNotification_Model_Resource_System_Message_Collection_Synchronized
    extends Magento_AdminNotification_Model_Resource_System_Message_Collection
{
    /**
     * Unread message list
     *
     * @var Magento_AdminNotification_Model_System_MessageInterface[]
     */
    protected $_unreadMessages = array();

    /**
     * Store new messages in database and remove outdated messages
     *
     * @return $this|Magento_Core_Model_Resource_Db_Abstract
     */
    public function _afterLoad()
    {
        $messages = $this->_messageList->asArray();
        $persisted = array();
        $unread = array();
        foreach ($messages as $message) {
            if ($message->isDisplayed()) {
                foreach ($this->_items as $persistedKey => $persistedMessage) {
                    if ($message->getIdentity() == $persistedMessage->getIdentity()) {
                        $persisted[$persistedKey] = $persistedMessage;
                        continue 2;
                    }
                }
                $unread[] = $message;
            }
        }
        $removed = array_diff_key($this->_items, $persisted);
        foreach ($removed as $removedItem) {
            $removedItem->delete();
        }
        foreach ($unread as $unreadItem ) {
            $item = $this->getNewEmptyItem();
            $item->setIdentity($unreadItem->getIdentity())
                ->setSeverity($unreadItem->getSeverity())
                ->save();
        }
        if (count($removed) || count($unread)) {
            $this->_unreadMessages = $unread;
            $this->clear();
            $this->load();
        } else {
            parent::_afterLoad();
        }
        return $this;
    }

    /**
     * Retrieve list of unread messages
     *
     * @return Magento_AdminNotification_Model_System_MessageInterface[]
     */
    public function getUnread()
    {
        return $this->_unreadMessages;
    }
}
