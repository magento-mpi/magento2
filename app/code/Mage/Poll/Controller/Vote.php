<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Poll
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Poll vote controller
 *
 * @file        Vote.php
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Poll_Controller_Vote extends Magento_Core_Controller_Front_Action
{
    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = array('add');

    /**
     * Add Vote to Poll
     *
     * @return void
     */
    public function addAction()
    {
        $pollId     = intval($this->getRequest()->getParam('poll_id'));
        $answerId   = intval($this->getRequest()->getParam('vote'));

        /** @var $poll Mage_Poll_Model_Poll */
        $poll = Mage::getModel('Mage_Poll_Model_Poll')->load($pollId);

        /**
         * Check poll data
         */
        if ($poll->getId() && !$poll->getClosed() && !$poll->isVoted()) {
            $vote = Mage::getModel('Mage_Poll_Model_Poll_Vote')
                ->setPollAnswerId($answerId)
                ->setIpAddress(Mage::helper('Magento_Core_Helper_Http')->getRemoteAddr(true))
                ->setCustomerId(Mage::getSingleton('Mage_Customer_Model_Session')->getCustomerId());

            $poll->addVote($vote);
            Mage::getSingleton('Magento_Core_Model_Session')->setJustVotedPoll($pollId);
            $this->_eventManager->dispatch(
                'poll_vote_add',
                array(
                    'poll'  => $poll,
                    'vote'  => $vote
                )
            );
        }
        $this->_redirectReferer();
    }
}
