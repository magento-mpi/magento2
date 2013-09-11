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
 * Poll answers model
 *
 * @method \Magento\Poll\Model\Resource\Poll\Answer _getResource()
 * @method \Magento\Poll\Model\Resource\Poll\Answer getResource()
 * @method int getPollId()
 * @method \Magento\Poll\Model\Poll\Answer setPollId(int $value)
 * @method string getAnswerTitle()
 * @method \Magento\Poll\Model\Poll\Answer setAnswerTitle(string $value)
 * @method int getVotesCount()
 * @method \Magento\Poll\Model\Poll\Answer setVotesCount(int $value)
 * @method int getAnswerOrder()
 * @method \Magento\Poll\Model\Poll\Answer setAnswerOrder(int $value)
 *
 * @category    Magento
 * @package     Magento_Poll
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Poll\Model\Poll;

class Answer extends \Magento\Core\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('\Magento\Poll\Model\Resource\Poll\Answer');
    }

    public function countPercent($poll)
    {
        $this->setPercent(
            round(($poll->getVotesCount() > 0 ) ? ($this->getVotesCount() * 100 / $poll->getVotesCount()) : 0, 2)
        );
        return $this;
    }

    protected function _afterSave()
    {
        \Mage::getModel('\Magento\Poll\Model\Poll')
            ->setId($this->getPollId())
            ->resetVotesCount();
    }

    protected function _beforeDelete()
    {
        $this->setPollId($this->load($this->getId())->getPollId());
    }

    protected function _afterDelete()
    {
        \Mage::getModel('\Magento\Poll\Model\Poll')
            ->setId($this->getPollId())
            ->resetVotesCount();
    }
}
