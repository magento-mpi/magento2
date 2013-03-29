<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Backend_Block_System_Messages_NewMessagePopup extends Mage_Backend_Block_Template
{
    /**
     * System Message list
     *
     * @var Mage_Backend_Model_System_MessageList
     */
    protected $_messageList;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Backend_Model_System_MessageList $messageList
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Backend_Model_System_MessageList $messageList,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_messageList = $messageList;
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if ($this->_messageList->getNew()) {
            return parent::_toHtml();
        }
        return '';
    }
}
