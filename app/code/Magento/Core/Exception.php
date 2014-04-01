<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core;

/**
 * Magento Core \Exception
 *
 * This class will be extended by other modules
 *
 * @category   Magento
 * @package    Magento_Core
 */
class Exception extends \Exception
{
    /**
     * @var array
     */
    protected $_messages = array();

    /**
     * @param \Magento\Message\AbstractMessage $message
     * @return $this
     */
    public function addMessage(\Magento\Message\AbstractMessage $message)
    {
        if (!isset($this->_messages[$message->getType()])) {
            $this->_messages[$message->getType()] = array();
        }
        $this->_messages[$message->getType()][] = $message;
        return $this;
    }

    /**
     * @param string $type
     * @return array
     */
    public function getMessages($type = '')
    {
        if ('' == $type) {
            $arrRes = array();
            foreach ($this->_messages as $messageType => $messages) {
                $arrRes = array_merge($arrRes, $messages);
            }
            return $arrRes;
        }
        return isset($this->_messages[$type]) ? $this->_messages[$type] : array();
    }

    /**
     * Set or append a message to existing one
     *
     * @param string $message
     * @param bool $append
     * @return \Magento\Core\Exception
     */
    public function setMessage($message, $append = false)
    {
        if ($append) {
            $this->message .= $message;
        } else {
            $this->message = $message;
        }
        return $this;
    }
}
