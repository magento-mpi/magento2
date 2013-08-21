<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Messages collection
 */
class Magento_Core_Model_Message_Collection
{
    /**
     * All messages by type array
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * @var string
     */
    protected $_lastAddedMessage;

    /**
     * Adding new message to collection
     *
     * @param   Magento_Core_Model_Message_Abstract $message
     * @return  Magento_Core_Model_Message_Collection
     */
    public function add(Magento_Core_Model_Message_Abstract $message)
    {
        return $this->addMessage($message);
    }

    /**
     * Adding new message to collection
     *
     * @param   Magento_Core_Model_Message_Abstract $message
     * @return  Magento_Core_Model_Message_Collection
     */
    public function addMessage(Magento_Core_Model_Message_Abstract $message)
    {
        if (!isset($this->_messages[$message->getType()])) {
            $this->_messages[$message->getType()] = array();
        }
        $this->_messages[$message->getType()][] = $message;
        $this->_lastAddedMessage = $message;
        return $this;
    }

    /**
     * Clear all messages except sticky
     *
     * @return Magento_Core_Model_Message_Collection
     */
    public function clear()
    {
        foreach ($this->_messages as $type => $messages) {
            foreach ($messages as $id => $message) {
                if (!$message->getIsSticky()) {
                    unset($this->_messages[$type][$id]);
                }
            }
            if (empty($this->_messages[$type])) {
                unset($this->_messages[$type]);
            }
        }
        return $this;
    }

    /**
     * Get last added message if any
     *
     * @return Magento_Core_Model_Message_Abstract|null
     */
    public function getLastAddedMessage()
    {
        return $this->_lastAddedMessage;
    }

    /**
     * Get first even message by identifier
     *
     * @param string $identifier
     * @return Magento_Core_Model_Message_Abstract|null
     */
    public function getMessageByIdentifier($identifier)
    {
        foreach ($this->_messages as $messages) {
            foreach ($messages as $message) {
                if ($identifier === $message->getIdentifier()) {
                    return $message;
                }
            }
        }
    }

    /**
     * Delete message by id
     *
     * @param string $identifier
     */
    public function deleteMessageByIdentifier($identifier)
    {
        foreach ($this->_messages as $type => $messages) {
            foreach ($messages as $id => $message) {
                if ($identifier === $message->getIdentifier()) {
                    unset($this->_messages[$type][$id]);
                }
                if (empty($this->_messages[$type])) {
                    unset($this->_messages[$type]);
                }
            }
        }
    }

    /**
     * Retrieve messages collection items
     *
     * @param   string $type
     * @return  array
     */
    public function getItems($type = null)
    {
        if ($type) {
            return isset($this->_messages[$type]) ? $this->_messages[$type] : array();
        }

        $arrRes = array();
        foreach ($this->_messages as $messages) {
            $arrRes = array_merge($arrRes, $messages);
        }

        return $arrRes;
    }

    /**
     * Retrieve all messages by type
     *
     * @param   string $type
     * @return  array
     */
    public function getItemsByType($type)
    {
        return isset($this->_messages[$type]) ? $this->_messages[$type] : array();
    }

    /**
     * Retrieve all error messages
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->getItemsByType(Magento_Core_Model_Message::ERROR);
    }

    /**
     * @return string
     */
    public function toString()
    {
        $out = '';
        $arrItems = $this->getItems();
        foreach ($arrItems as $item) {
            $out .= $item->toString();
        }

        return $out;
    }

    /**
     * Retrieve messages count
     *
     * @param null|string $type
     * @return int
     */
    public function count($type = null)
    {
        if ($type) {
            if (isset($this->_messages[$type])) {
                return count($this->_messages[$type]);
            }
            return 0;
        }
        return count($this->_messages);
    }
}
