<?php
/**
 * Poll vote controller
 *
 * @file        Vote.php
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski (hacki) alexander@varien.com
 */

class Mage_Poll_VoteController extends Mage_Core_Controller_Front_Action
{
    /**
     * Add vote action
     *
     * @access public
     * @return void
     */
    public function addAction()
    {
        if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
            $this->getResponse()->setRedirect($referer);
        }

        $pollId = intval( $this->getRequest()->getParam('poll_id') );
        $answerId = intval( $this->getRequest()->getParam('vote') );

        if( $pollId === 0 || $answerId === 0 || Mage::getSingleton('poll/poll')->isVoted($pollId) ) {
            return;
        }

        Mage::getSingleton('poll/poll_vote')
            ->setPollId( $pollId )
            ->setIpAddress( ip2long($this->getRequest()->getServer('REMOTE_ADDR')) )
            ->setCustomerId( Mage::getSingleton('customer/session')->getCustomerId() )
            ->setPollAnswerId($answerId)
            ->addVote();

        Mage::getSingleton('poll/poll')->setVoted($pollId);
    }
}