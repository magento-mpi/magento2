<?php
class Mage_Poll_Model_Mysql4_Poll_Answer
{
    protected $_pollAnswerTable;

    protected $_read;
    protected $_write;

    protected $_answerId;

    function __construct()
    {
        $this->_pollAnswerTable = Mage::getSingleton('core/resource')->getTableName('poll/poll_answer');

        $this->_read = Mage::getSingleton('core/resource')->getConnection('poll_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('poll_write');
    }

    function save($answer)
    {
        if( $answer->getAnswerId() ) {
            $condition = $this->_write->quoteInto("{$this->_pollAnswerTable}.answer_id=?", $answer->getAnswerId());
            $this->_write->update($this->_pollAnswerTable, $answer->getData(), $condition);
        } else {
            $this->_write->insert($this->_pollAnswerTable, $answer->getData());
        }
    }

    function delete($answer)
    {
        if( $answer->getAnswerId() ) {
            $condition = $this->_write->quoteInto("{$this->_pollAnswerTable}.answer_id=?", $answer->getAnswerId());
            $this->_write->delete($this->_pollAnswerTable, $condition);
        }
    }

    function load()
    {
        if( $this->getId() ) {
            $condition = $this->_read->quoteInto("{$this->_pollAnswerTable}.answer_id=?", $this->getId());

            $select = $this->_read->select();
            $select->from($this->_pollAnswerTable);
            $select->where($condition);

            return $this->_read->fetchRow($select);
        }
    }

    function loadAnswers($pollId)
    {
        if( intval($pollId) > 0 ) {
            $condition = $this->_read->quoteInto("{$this->_pollAnswerTable}.poll_id=?", $pollId);

            $select = $this->_read->select();
            $select->from($this->_pollAnswerTable);
            $select->where($condition);

            return $this->_read->fetchAll($select);
        }
    }

    function setId($answerId)
    {
        $this->_answerId = intval($answerId);
        return $this;
    }

    function getId()
    {
        return $this->_answerId;
    }

    public function getPercent($totalVotesCount, $answerVotesCount)
    {
        return round(( $totalVotesCount > 0 ) ? ($answerVotesCount * 100 / $totalVotesCount) : 0);
    }
}