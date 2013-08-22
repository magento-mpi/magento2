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
 * Poll block
 *
 * @file        Poll.php
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Poll_Block_ActivePoll extends Magento_Core_Block_Template
{
    /**
     * Poll templates
     *
     * @var array
     */
    protected $_templates;

    /**
     * Current Poll Id
     *
     * @var int
     */
    protected $_pollId = null;

    /**
     * Already voted by current visitor Poll Ids array
     *
     * @var array|null
     */
    protected $_votedIds = null;

    /**
     * Poll model
     *
     * @var Magento_Poll_Model_Poll
     */
    protected $_pollModel;

    protected function _construct()
    {
        parent::_construct();
        $this->_pollModel = Mage::getModel('Magento_Poll_Model_Poll');
    }

    /**
     * Set current Poll Id
     *
     * @param int $pollId
     * @return Magento_Poll_Block_ActivePoll
     */
    public function setPollId($pollId)
    {
        $this->_pollId = $pollId;
        return $this;
    }

    /**
     * Get current Poll Id
     *
     * @return int|null
     */
    public function getPollId()
    {
        return $this->_pollId;
    }

    /**
     * Retrieve already voted Poll Ids
     *
     * @return array|null
     */
    public function getVotedPollsIds()
    {
        if ($this->_votedIds === null) {
            $this->_votedIds = $this->_pollModel->getVotedPollsIds();
        }
        return $this->_votedIds;
    }

    /**
     * Get Ids of all active Polls
     *
     * @return array
     */
    public function getActivePollsIds()
    {
        return $this->_pollModel
            ->setExcludeFilter($this->getVotedPollsIds())
            ->setStoreFilter(Mage::app()->getStore()->getId())
            ->getAllIds();
    }

    /**
     * Get Poll Id to show
     *
     * @return int
     */
    public function getPollToShow()
    {
        if ($this->getPollId()) {
            return $this->getPollId();
        }
        // get last voted poll (from session only)
        $pollId = Mage::getSingleton('Magento_Core_Model_Session')->getJustVotedPoll();
        if (empty($pollId)) {
            // get random not voted yet poll
            $votedIds = $this->getVotedPollsIds();
            $pollId = $this->_pollModel
                ->setExcludeFilter($votedIds)
                ->setStoreFilter(Mage::app()->getStore()->getId())
                ->getRandomId();
        }
        $this->setPollId($pollId);

        return $pollId;
    }

    /**
     * Get Poll related data
     *
     * @param int $pollId
     * @return array|bool
     */
    public function getPollData($pollId)
    {
        if (empty($pollId)) {
            return false;
        }
        $poll = $this->_pollModel->load($pollId);

        $pollAnswers = Mage::getModel('Magento_Poll_Model_Poll_Answer')
            ->getResourceCollection()
            ->addPollFilter($pollId)
            ->load()
            ->countPercent($poll);

        // correct rounded percents to be always equal 100
        $percentsSorted = array();
        $answersArr = array();
        foreach ($pollAnswers as $key => $answer) {
            $percentsSorted[$key] = $answer->getPercent();
            $answersArr[$key] = $answer;
        }
        asort($percentsSorted);
        $total = 0;
        foreach ($percentsSorted as $key => $value) {
            $total += $value;
        }
        // change the max value only
        if ($total > 0 && $total !== 100) {
            $answersArr[$key]->setPercent($value + 100 - $total);
        }

        return array(
            'poll' => $poll,
            'poll_answers' => $pollAnswers,
            'action' => Mage::getUrl('poll/vote/add', array('poll_id' => $pollId, '_secure' => true))
        );
    }


    /**
     * Add poll template
     *
     * @param string $template
     * @param string $type
     * @return Magento_Poll_Block_ActivePoll
     */
    public function setPollTemplate($template, $type)
    {
        $this->_templates[$type] = $template;
        return $this;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $coreSessionModel Magento_Core_Model_Session */
        $coreSessionModel = Mage::getSingleton('Magento_Core_Model_Session');
        $justVotedPollId = $coreSessionModel->getJustVotedPoll();
        if ($justVotedPollId && !$this->_pollModel->isVoted($justVotedPollId)) {
            $this->_pollModel->setVoted($justVotedPollId);
        }

        $pollId = $this->getPollToShow();
        $data = $this->getPollData($pollId);
        $this->assign($data);

        $coreSessionModel->setJustVotedPoll(false);

        if ($this->_pollModel->isVoted($pollId) === true || $justVotedPollId) {
            $this->setTemplate($this->_templates['results']);
        } else {
            $this->setTemplate($this->_templates['poll']);
        }
        return parent::_toHtml();
    }


    /**
     * Get cache key informative items that must be preserved in cache placeholders
     * for block to be rerendered by placeholder
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $items = array(
            'templates' => serialize($this->_templates)
        );

        $items = parent::getCacheKeyInfo() + $items;

        return $items;
    }

}
