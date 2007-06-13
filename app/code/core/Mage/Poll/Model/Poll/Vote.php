<?php

class Mage_Poll_Model_Vote extends Varien_Object
{
    protected $_pollId;

    public function getId()
    {
        return $this->getPollId();
    }

    public function addVote()
    {
        return $this;
    }

    protected function _getResource()
    {
        return  Mage::getSingleton('poll/');
    }
}