<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Mage_Backend_Model_Resource_System_Message_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * System message list
     *
     * @var Mage_Backend_Model_System_MessageList
     */
    protected $_messageList;

    /**
     * Retrieve unread message list
     *
     * @var Mage_Backend_Model_System_MessageInterface[]
     */
    protected $_unreadMessages = array();

    /**
     * @param Mage_Backend_Model_System_MessageList $messageList
     * @param null $resource
     */
    public function __construct(Mage_Backend_Model_System_MessageList $messageList, $resource = null)
    {
        $this->_messageList = $messageList;
        parent::__construct($resource);
    }

    /**
     * Resource collection initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_Backend_Model_System_Message', 'Mage_Backend_Model_Resource_System_Message');
    }

    /**
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addOrder('severity', self::SORT_ORDER_ASC)
            ->addOrder('created_at');
    }

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
                if ($message->getText() == $persistedMessage->getText()) {
                    $persisted = true;
                    if (!$message->isDisplayed()) {
                        $persistedMessage->delete();
                        unset($this->_items[$persistedKey]);
                        $reloadRequired = true;
                    }
                    break;
                }
            }
            if (!$persisted && $message->isDisplayed()) {
                $item = $this->getNewEmptyItem();
                $item->setText($message->getText())
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
        return $this;
    }

    /**
     * @return Mage_Backend_Model_System_MessageInterface[]
     */
    public function getUnread()
    {
        return $this->_unreadMessages;
    }
}
