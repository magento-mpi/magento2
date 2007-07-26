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

        $pollId = 1;

        $poll = Mage::getModel('poll/poll')
                        ->load($pollId)
                        ->loadAnswers()
                        ->calculatePercent();

        $this->assign('poll', $poll)
             ->assign('action', Mage::getUrl('poll/vote/add/poll_id/'.$pollId));

        $this->_voted = Mage::getModel('poll/poll')->isVoted($pollId);
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
