<?php
/**
 * Massages block
 *
 * @package    Ecom
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Block_Message extends Mage_Core_Block_Abstract
{
    /**
     * Messages
     *
     * @var Mage_Core_Message
     */
    protected $_message;
    
    public function setMessage(Mage_Core_Message $message)
    {
        $this->_message = $message;
    }
    
    public function toString()
    {
        //TODO: genedate by mesage types
        $arrMessages = $this->_message->getMessages();
        $out = '';
        foreach ($arrMessages as $message) {
            $out.= '<div class="' . $message->getType() . '">';
            $out.= $message->getData();
            $out.= '</div>';
        }
        return $out;
    }
}