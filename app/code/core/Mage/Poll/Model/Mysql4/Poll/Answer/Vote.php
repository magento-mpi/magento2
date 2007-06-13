<?php

class Mage_Poll_Mysql4_Poll_Vote
{
    protected $_pollId;
    protected $_pollVoteTable;

    function __construct($pollId)
    {
        $this->_pollId = $pollId;
    }

    function add()
    {
        $this->_pollVoteTable = Mage::getSingleton('core/resource')->getTableName('poll_resource', 'poll_vote');
    }
}