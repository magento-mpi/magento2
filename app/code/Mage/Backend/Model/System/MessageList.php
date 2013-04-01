<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Backend_Model_System_MessageList
{
    /**
     * List of configured message classes
     *
     * @var array
     */
    protected $_messageClasses;

    /**
     * @var array
     */
    protected $_messages = array();

    /**
     * @var Mage_Core_Model_Resource
     */
    protected $_resource;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Resource $resource
     * @param Mage_Backend_Model_Resource_System_Message_Collection $messageCollection
     * @param array $messages
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Resource $resource,
        Mage_Backend_Model_Resource_System_Message_Collection $messageCollection,
        $messages = array()
    ) {
        $this->_objectManager = $objectManager;
        $this->_resource = $resource;
        $this->_messageCollection = $messageCollection;
        $this->_messageClasses = $messages;
    }

    /**
     * Load messages to display
     *
     * @throws InvalidArgumentException
     */
    protected function _loadMessages()
    {
        if (!$this->_messages) {
            usort($this->_messageClasses, function($el1, $el2) {
                return isset($el1['priority']) ?
                    (isset($el2['priority']) ? ($el1['priority'] > $el2['priority'] ? true : false) : true) :
                    false;
            });
            $objectManager = $this->_objectManager;
            $this->_messages = array_walk($this->_messageClasses, function($messageConfig, $key) use ($objectManager) {
                if (!isset($messageConfig['class'])) {
                    throw new InvalidArgumentException('Message class for message ' . $key . ' is not set');
                }
                return $objectManager->get($messageConfig['class']);
            });
            $this->_unreadMessages = $this->_messageCollection->synchronize($this->_messages);
        }
    }

    /**
     * Get unread messages list
     *
     * @return array
     */
    public function getUnread()
    {
        $this->_loadMessages();
        return $this->_unreadMessages;
    }

    /**
     * Retrieve list of all messages
     *
     * @return array
     */
    public function asArray()
    {
        $this->_loadMessages();
        return $this->_messages;
    }
}
