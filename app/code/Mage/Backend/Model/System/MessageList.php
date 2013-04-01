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
            $messages = array();
             array_walk($this->_messageClasses, function($messageConfig, $key) use ($objectManager, &$messages) {
                if (!isset($messageConfig['class'])) {
                    throw new InvalidArgumentException('Message class for message ' . $key . ' is not set');
                }
                $messages[] = $objectManager->get($messageConfig['class']);
            });
            $this->_messages = $messages;
        }
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
