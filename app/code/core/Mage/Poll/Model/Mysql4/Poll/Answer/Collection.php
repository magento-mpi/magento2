<?php
class Mage_Poll_Model_Mysql4_Poll_Answer_Collection extends Varien_Data_Collection_Db
{
    protected $_pollAnswerTable;

    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('poll_read'));

        $this->_pollAnswerTable = Mage::getSingleton('core/resource')->getTableName('poll_resource', 'poll_answer');

        $this->_sqlSelect
            ->from($this->_pollAnswerTable);

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('poll/poll'));
    }

    public function loadData()
    {
        parent::loadData();
        return $this;
    }

    public function addPollFilter($pollId)
    {
        $this->addFilter('poll_id', $pollId);
        return $this;
    }

    public function getItems()
    {
        $this->load();
        return $this->_items;
    }
}