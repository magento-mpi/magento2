<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Mage_Backend_Model_Resource_System_Message_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
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
        $this->addOrder('severity', self::SORT_ORDER_ASC)
            ->addOrder('created_at');
    }
}
