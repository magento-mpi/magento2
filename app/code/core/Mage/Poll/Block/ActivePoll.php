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
        $pollId = 2;
        $pollCollection = Mage::getSingleton('poll_resource/poll_collection');
        $pollCollection->addPollFilter($pollId);
        $data = $pollCollection->load()->getAnswers();

        $this->assign('data', $data)
             ->assign('action', Mage::getUrl('poll', array('controller'=>'vote', 'action'=>'add', 'poll_id'=>$pollId)));

        $voted = true; /* FIXME */
        if( $voted === true ) {
            $this->setTemplate('poll/result.phtml');
        } else {
            $this->setTemplate('poll/active.phtml');
        }
    }
}