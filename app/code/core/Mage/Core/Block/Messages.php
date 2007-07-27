<?php
/**
 * Messages block
 *
 * @package     Mage
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Block_Messages extends Mage_Core_Block_Abstract 
{
    /**
     * Messages collection
     *
     * @var Mage_Core_Model_Message_Collection
     */
    protected $_messages;
    
    /**
     * Set messages collection
     *
     * @param   Mage_Core_Model_Message_Collection $messages
     * @return  Mage_Core_Block_Messages
     */
    public function setMessages(Mage_Core_Model_Message_Collection $messages)
    {
        $this->_messages = $messages;
        return $this;
    }
    
    /**
     * Retrieve messages collection
     *
     * @return Mage_Core_Model_Message_Collection
     */
    public function getMessageCollection()
    {
        if (!($this->_messages instanceof Mage_Core_Model_Message_Collection)) {
            $this->_messages = Mage::getModel('core/message_collection');
        }
        return $this->_messages;
    }
    
    /**
     * Retrieve messages array by message type
     *
     * @param   string $type
     * @return  array
     */
    public function getMessages($type=null)
    {
        return $this->getMessageCollection()->getItems($type);
    }
    
    /**
     * Retrieve messages in HTML format
     *
     * @param   string $type
     * @return  string
     */
    public function getHtml($type=null)
    {
        $html = '<ul>';
        foreach ($this->getMessages($type) as $message) {
        	$html.= '<li class="'.$message->getType().'-msg">'.$message->getText().'</li>';
        }
        $html .= '</ul>';
        return $html;
    }
}
