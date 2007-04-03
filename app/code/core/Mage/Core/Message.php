<?php

/**
 * Class to hold messages between submition from code and reporting to user
 *
 */
class Mage_Core_Message
{
    /**
     * All messages registry
     *
     * @var array
     */
    protected $_messages = array();
    
    /**
     * Messages by type
     *
     * @var array
     */
    protected $_messagesByType = array();
    
    /**
     * Constructor
     *
     */
    function __construct()
    {
    }
    
/*    function addMessage(Mage_Core_Message_Abstract $message)
    {
        $this->_messages[] = $message;
        $this->_messagesByType[$message->getType()][] = $message;
        
        $this->_sessionNamespace->messages = $this->_messages;
    }*/
    
    /**
     * Add message
     *
     * @param string|Mage_Core_Message_Abstract $message
     * @param string $messageType
     */
    function addMessage($message, $messageType = 'notice')
    {
        if (is_string($message)) {
            
        }
        switch (strtolower($messageType)) {
        	case 'error':
        		$message = new Mage_Core_Message_Error($message);
        		break;
            case 'warning':
                $message = new Mage_Core_Message_Warning($message);
                break;
        	default:
        	    $message = new Mage_Core_Message_Notice($message);
        		break;
        }
        
        $this->_messages[] = $message;
        $this->_messagesByType[$message->getType()][] = $message;
    }    

    /**
     * Get all messages or by type
     *
     * @param string $type
     * @return array
     */
    function getMessages($type='')
    {
        if (''===$type) {
            return $this->_messages;
        } else {
            return $this->_messagesByType[$type];
        }
    }
}