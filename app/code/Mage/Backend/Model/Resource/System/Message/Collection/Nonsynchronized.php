<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Mage_Backend_Model_Resource_System_Message_Collection_Nonsynchronized
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * System message list
     *
     * @var Mage_Backend_Model_System_MessageList
     */
    protected $_messageList;

    /**
     * @param Mage_Backend_Model_System_MessageList $messageList
     * @param null $resource
     */
    public function __construct(Mage_Backend_Model_System_MessageList $messageList, $resource = null)
    {
        $this->_messageList = $messageList;
        parent::__construct($resource);
    }

    /**
     * Resource collection initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_Backend_Model_System_Message', 'Mage_Backend_Model_Resource_System_Message');
    }

    /**
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addOrder('created_at');
    }

    /**
     * Set message severity filter
     *
     * @param $severity
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    public function setSeverity($severity)
    {
        $this->addFieldToFilter('severity', array('eq' => $severity * 1));
        return $this;
    }

    /**
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $key => $item) {
            $message = $this->_messageList->getMessageByIdentity($item->getIdentity());
            if ($message) {
                $item->setText($message->getText());
                $item->setLink($message->getLink());
            } else {
                unset($this->_items[$key]);
            }
        }
    }
}
