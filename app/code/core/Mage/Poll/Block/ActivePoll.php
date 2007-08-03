<?php
/**
 * Poll block
 *
 * @file        Poll.php
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski (hacki) alexander@varien.com
 * @author      Sergiy Lysak <sergey@varien.com>
 */

class Mage_Poll_Block_ActivePoll extends Mage_Core_Block_Template
{
    protected $_templates, $_voted;

    public function __construct()
    {
        parent::__construct();

        $pollModel = Mage::getModel('poll/poll');
        $pollId = ( Mage::getSingleton('core/session')->getJustVotedPoll() ) ? Mage::getSingleton('core/session')->getJustVotedPoll() : $pollModel->getRandomId();
        $poll = $pollModel->load($pollId);

        if( !$pollId ) {
            return false;
        }

        $pollAnswers = Mage::getModel('poll/poll_answer')
            ->getResourceCollection()
            ->addPollFilter($pollId)
            ->load()
            ->countPercent($poll);

        $this->assign('poll', $poll)
             ->assign('poll_answers', $pollAnswers)
             ->assign('action', Mage::getUrl('poll/vote/add/poll_id/'.$pollId));

        $this->_voted = Mage::getModel('poll/poll')->isVoted($pollId);
        Mage::getSingleton('core/session')->setJustVotedPoll(false);
    }

    public function setPollTemplate($template, $type)
    {
        $this->_templates[$type] = $template;
        return $this;
    }

    public function toHtml()
    {
        if( $this->_voted === true ) {
            $this->setTemplate($this->_templates['results']);
        } else {
            $this->setTemplate($this->_templates['poll']);
        }
        return parent::toHtml();
    }
}
