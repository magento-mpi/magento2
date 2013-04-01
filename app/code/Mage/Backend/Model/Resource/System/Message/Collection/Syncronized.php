<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Mage_Backend_Model_Resource_System_Messages_Syncronized extends Mage_Backend_Model_System_Message_Collection
{
    /**
     * Retrieve unread message list
     *
     * @var Mage_Backend_Model_System_MessageInterface[]
     */
    protected $_unreadMessages = array();

    /**
     * @var array
     */
    protected $_countBySeverity = array();

    /**
     * Load synchronized messages
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this|Varien_Data_Collection_Db
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        parent::load($printQuery, $logQuery);
        $messages = $this->_messageList->asArray();
        $reloadRequired = false;
        foreach ($messages as $message) {
            $persisted = false;
            foreach ($this->_items as $persistedKey => $persistedMessage) {
                if ($message->getIdentity() == $persistedMessage->getIdentity()) {
                    $persisted = true;
                    if (!$message->isDisplayed()) {
                        $persistedMessage->delete();
                        unset($this->_items[$persistedKey]);
                        $reloadRequired = true;
                    }
                    $persistedMessage->setPersisted(true);
                    break;
                }
            }
            if (!$persisted && $message->isDisplayed()) {
                $item = $this->getNewEmptyItem();
                $item->setIdentity($message->getIdentity())
                    ->setSeverity($message->getSeverity())
                    ->save();
                $this->_unreadMessages[] = $item;
                $reloadRequired = true;
            }
        }
        if ($reloadRequired) {
            $this->clear();
            parent::load($printQuery, $logQuery);
        }
        foreach ($this->_items as $item) {
            $message = $this->_messageList->getMessageByIdentity($item->getIdentity());
            $item->setText($message->getText());
            $item->setLink($message->getLink());
            if (array_key_exists($message->getSeverity(), $this->_countBySeverity)) {
                $this->_countBySeverity[$message->getSeverity()]++;
            } else {
                $this->_countBySeverity[$message->getSeverity()] = 1;
            }
        }
        return $this;
    }

    /**
     * @return Mage_Backend_Model_System_MessageInterface[]
     */
    public function getUnread()
    {
        return $this->_unreadMessages;
    }

    /**
     * @param int $severity
     * @return int
     */
    public function getCountBySeverity($severity)
    {
        return isset($this->_countBySeverity[$severity]) ? $this->_countBySeverity[$severity] : 0;
    }
}
