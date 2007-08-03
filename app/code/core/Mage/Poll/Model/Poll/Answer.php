<?php
/**
 * Poll answers model
 *
 * @package     Mage
 * @subpackage  Poll
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Poll_Model_Poll_Answer extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('poll/poll_answer');
    }

    public function countPercent($poll)
    {
        $this->setPercent(round(( $poll->getVotesCount() > 0 ) ? ($this->getVotesCount() * 100 / $poll->getVotesCount()) : 0));
        return $this;
    }

    protected function _afterSave()
    {
        Mage::getModel('poll/poll')
            ->setId($this->getPollId())
            ->resetVotesCount();
    }

    protected function _beforeDelete()
    {
        $this->setPollId($this->load($this->getId())->getPollId());
    }

    protected function _afterDelete()
    {
        Mage::getModel('poll/poll')
            ->setId($this->getPollId())
            ->resetVotesCount();
    }
}