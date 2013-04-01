<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Mage_Backend_Model_Resource_System_Message_Collection_Nonsynchronized
    extends Mage_Backend_Model_Resource_System_Message_Collection
{
    /**
     * Resource collection initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_Backend_Model_System_Message', 'Mage_Backend_Model_Resource_System_Message');
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
