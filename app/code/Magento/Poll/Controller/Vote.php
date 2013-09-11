<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Poll
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Poll vote controller
 *
 * @file        Vote.php
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Poll\Controller;

class Vote extends \Magento\Core\Controller\Front\Action
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

        /** @var $poll \Magento\Poll\Model\Poll */
        $poll = \Mage::getModel('Magento\Poll\Model\Poll')->load($pollId);

        /**
         * Check poll data
         */
        if ($poll->getId() && !$poll->getClosed() && !$poll->isVoted()) {
            $vote = \Mage::getModel('Magento\Poll\Model\Poll\Vote')
                ->setPollAnswerId($answerId)
                ->setIpAddress(\Mage::helper('Magento\Core\Helper\Http')->getRemoteAddr(true))
                ->setCustomerId(\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId());

            $poll->addVote($vote);
            \Mage::getSingleton('Magento\Core\Model\Session')->setJustVotedPoll($pollId);
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
