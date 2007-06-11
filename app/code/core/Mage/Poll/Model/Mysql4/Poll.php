<?php
/**
 * Poll
 *
 * @file        Poll.php
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski (hacki) alexander@varien.com
 */

class Mage_Poll_Model_Mysql4_Poll 
{

    protected $_pollTable;

    protected $_pollAnswerTable;

    protected $_pollVoteTable;

    protected $_website_id;

    function __construct()
    {
        $this->_pollTable = 'poll';
        $this->_pollAnswerTable = 'poll_answer';
        $this->_pollVoteTable = 'poll_vote';

        $this->_read = Mage::registry('resources')->getConnection('core_read');
        $this->_write = Mage::registry('resources')->getConnection('core_write');
    }

    public function loadPolls()
    {
        $sql = "SELECT * FROM {$this->_pollTable} WHERE `status` = '0' AND `website_id` = '{$this->getWebsiteId()}'";
        $polls = $this->_read->fetchAll($sql);

        foreach( $polls as $poll_key => $poll ) {
            $answers = $this->_getPollAnswers($poll['poll_id'], $votes_count);
            $polls[$poll_key]['poll_answers'] = $answers;
        }

        return $polls;
    }

    protected function _getPollAnswers($poll_id, $votes_count)
    {
        $sql = "SELECT * FROM {$this->_pollAnswerTable} WHERE `poll_id` = '{$poll_id}'";
        $answers = $this->_read->fetchAll($sql);

        foreach( $answers as $answer_key => $answer ) {
            $answers[$answer_key]['persent'] = ceil( ($poll['votes_count'] / 100) * $answers['votes_count'] );
        }

        return $answers;
    }

    public function setWebsiteId($website_id)
    {
        $this->_website_id = $website_id;
    }

    protected function getWebsiteId()
    {
        return $this->_website_id;
    }

}  
 
// ft:php
// fileformat:unix
// tabstop:4
