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
    protected $_messages;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param array $messages
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        $messages = array()
    ) {
        $this->_objectManager = $objectManager;
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
            $objectManager = $this->_objectManager;
            foreach ($this->_messageClasses as $messageConfig) {
                if (!isset($messageConfig['class'])) {
                    throw new InvalidArgumentException('Message class for message ' . $key . ' is not set');
                }
                $message = $objectManager->get($messageConfig['class']);
                $this->_messages[$message->getIdentity()] = $message;
            }
        }
    }

    /**
     * @param string $identity
     * @return null|Mage_Backend_Model_System_MessageInterface
     */
    public function getMessageByIdentity($identity)
    {
        return isset($this->_messages[$identity]) ? $this->_messages[$identity] : null;
    }

    /**
     * Retrieve list of all messages
     *
     * @return Mage_Backend_Model_System_MessageInterface[]
     */
    public function asArray()
    {
        $this->_loadMessages();
        return $this->_messages;
    }
}
