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
        parent::loadData(true);
        return $this;
    }

    public function addPollFilter($arrPollId)
    {
        if( !$arrPollId ) {
            return;
        }

        $condition = 'poll_id';
        if( is_array($arrPollId) ) {
            $inString.= '\'' . join('\', \'', $arrPollId) . '\'';
            $condition.= ' IN(' . $inString . ')';
        } else {
            $condition = ' = ' . $arrPollId;
        }
        $this->addFilter(null, $condition, 'string');
    }

    function getPollAnswers($pollData)
    {
        $arr = array();
        foreach( $this->_items as $key => $item ) {
            if( $item->getPollId() == $pollData->getPollId() ) {
                $arr[] = $item->getData();
            }
        }
        return $arr;
    }
}