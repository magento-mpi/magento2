<?php
/**
 * Poll block
 *
 * @file        Poll.php
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski (hacki) alexander@varien.com
 */

class Mage_Poll_Block_ActivePoll extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();

        $pollId = 1;
        $pollCollection = Mage::getSingleton('poll/poll')->getCollection();
        $polls = $pollCollection
                        ->addPollFilter($pollId)
                        ->load()
                        ->addAnswers();

        $this->assign('polls', $polls)
             ->assign('action', Mage::getUrl('poll', array('controller'=>'vote', 'action'=>'add', 'poll_id'=>$pollId)));

        $voted = Mage::getSingleton('poll/poll')->isVoted($pollId); /* FIXME */
        if( $voted === true ) {
            $this->setTemplate('poll/result.phtml');
        } else {
            $this->setTemplate('poll/active.phtml');
        }
    }
}