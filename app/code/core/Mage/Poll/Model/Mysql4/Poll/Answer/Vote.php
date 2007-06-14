<?php

class Mage_Poll_Model_Mysql4_Poll_Answer_Vote
{
    protected $_pollVoteTable;
    protected $_pollAnswerTable;
    protected $_pollTable;

    protected $_read;
    protected $_write;

    function __construct()
    {
        $this->_pollVoteTable = Mage::getSingleton('core/resource')->getTableName('poll_resource', 'poll_vote');
        $this->_pollAnswerTable = Mage::getSingleton('core/resource')->getTableName('poll_resource', 'poll_answer');
        $this->_pollTable = Mage::getSingleton('core/resource')->getTableName('poll_resource', 'poll');

        $this->_read = Mage::getSingleton('core/resource')->getConnection('poll_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('poll_write');
    }

    function add($voteParams)
    {
        $this->_write->insert($this->_pollVoteTable, $voteParams);

        # Increment `poll_answer` votes count
        $pollAnswerData = array(
                            'votes_count' => new Zend_Db_Expr('votes_count+1')
                        );

        $condition = $this->_write->quoteInto("{$this->_pollAnswerTable}.answer_id=?", $voteParams['poll_answer_id']);
        $this->_write->update($this->_pollAnswerTable, $pollAnswerData, $condition);

        # Increment `poll` votes count
        $pollData = array(
                            'votes_count' => new Zend_Db_Expr('votes_count+1')
                        );

        $condition = $this->_write->quoteInto("{$this->_pollTable}.poll_id=?", $voteParams['poll_id']);
        $this->_write->update($this->_pollTable, $pollData, $condition);
    }
}