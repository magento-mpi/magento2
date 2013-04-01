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
            $this->_messages = array_walk($this->_messageClasses, function($messageConfig, $key) use ($objectManager) {
                if (!isset($messageConfig['class'])) {
                    throw new InvalidArgumentException('Message class for message ' . $key . ' is not set');
                }
                return $objectManager->get($messageConfig['class']);
            });
        }
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
