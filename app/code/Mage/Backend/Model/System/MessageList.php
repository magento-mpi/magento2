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
     * @param Magento_ObjectManager $objectManager
     * @param array $messages
     * @throws InvalidArgumentException
     */
    public function __construct(Magento_ObjectManager $objectManager, Mage_Core_Model_Cache $cache, $messages = array())
    {
        usort($messages, function($el1, $el2) {
            return isset($el1['priority']) ?
                (isset($el2['priority']) ? ($el1['priority'] > $el2['priority'] ? true : false) : true) :
                false;
        });
        $this->_messages = array_walk($messages, function($messageConfig, $key) use ($objectManager) {
            if (!isset($messageConfig['class'])) {
                throw new InvalidArgumentException('Message class for message ' . $key . ' is not set');
            }
            return $objectManager->get($messageConfig['class']);
        });
    }

    public function getNew()
    {
        
    }

    public function asArray()
    {
        return $this->_messages;
    }
}
