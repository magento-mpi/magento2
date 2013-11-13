<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Message;

/**
 * Messages collection
 */
class Collection
{
    /**
     * All messages by type array
     *
     * @var array
     */
    protected $messages = array();

    /**
     * @var string
     */
    protected $lastAddedMessage;

    /**
     * Adding new message to collection
     *
     * @param \Magento\Message\AbstractMessage $message
     * @return \Magento\Message\Collection
     */
    public function add(\Magento\Message\AbstractMessage $message)
    {
        return $this->addMessage($message);
    }

    /**
     * Adding new message to collection
     *
     * @param \Magento\Message\AbstractMessage $message
     * @return \Magento\Message\Collection
     */
    public function addMessage(\Magento\Message\AbstractMessage $message)
    {
        if (!isset($this->messages[$message->getType()])) {
            $this->messages[$message->getType()] = array();
        }
        $this->messages[$message->getType()][] = $message;
        $this->lastAddedMessage = $message;
        return $this;
    }

    /**
     * Clear all messages except sticky
     *
     * @return \Magento\Message\Collection
     */
    public function clear()
    {
        foreach ($this->messages as $type => $messages) {
            foreach ($messages as $id => $message) {
                /** @var $message \Magento\Message\AbstractMessage */
                if (!$message->getIsSticky()) {
                    unset($this->messages[$type][$id]);
                }
            }
            if (empty($this->messages[$type])) {
                unset($this->messages[$type]);
            }
        }
        return $this;
    }

    /**
     * Get last added message if any
     *
     * @return \Magento\Message\AbstractMessage|null
     */
    public function getLastAddedMessage()
    {
        return $this->lastAddedMessage;
    }

    /**
     * Get first even message by identifier
     *
     * @param string $identifier
     * @return \Magento\Message\AbstractMessage|null
     */
    public function getMessageByIdentifier($identifier)
    {
        foreach ($this->messages as $messages) {
            foreach ($messages as $message) {
                /** @var $message \Magento\Message\AbstractMessage */
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
        foreach ($this->messages as $type => $messages) {
            foreach ($messages as $id => $message) {
                /** @var $message \Magento\Message\AbstractMessage */
                if ($identifier === $message->getIdentifier()) {
                    unset($this->messages[$type][$id]);
                }
                if (empty($this->messages[$type])) {
                    unset($this->messages[$type]);
                }
            }
        }
    }

    /**
     * Retrieve messages collection items
     *
     * @param string $type
     * @return array
     */
    public function getItems($type = null)
    {
        if ($type) {
            return isset($this->messages[$type]) ? $this->messages[$type] : array();
        }

        $arrRes = array();
        foreach ($this->messages as $messages) {
            $arrRes = array_merge($arrRes, $messages);
        }

        return $arrRes;
    }

    /**
     * Retrieve all messages by type
     *
     * @param string $type
     * @return array
     */
    public function getItemsByType($type)
    {
        return isset($this->messages[$type]) ? $this->messages[$type] : array();
    }

    /**
     * Retrieve all error messages
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->getItemsByType(\Magento\Message\Factory::ERROR);
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
            if (isset($this->messages[$type])) {
                return count($this->messages[$type]);
            }
            return 0;
        }
        return count($this->messages);
    }
}
