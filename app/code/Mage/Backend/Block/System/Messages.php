<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Backend_Block_System_Messages extends Mage_Backend_Block_Template
{
    /**
     * Message list
     *
     * @var Mage_Backend_Model_System_MessageList
     */
    protected $_messages;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Backend_Model_Resource_System_Message_Collection $messages
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Backend_Model_Resource_System_Message_Collection $messages,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_messages = $messages;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (count($this->_messages->getItems())) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Retrieve message list
     *
     * @return Mage_Backend_Model_System_MessageInterface[]
     */
    public function getMessageList()
    {
        return $this->_messages->getItems();
    }
}
