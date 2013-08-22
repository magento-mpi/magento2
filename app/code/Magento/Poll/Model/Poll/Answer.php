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
 * @method Magento_Poll_Model_Resource_Poll_Answer _getResource()
 * @method Magento_Poll_Model_Resource_Poll_Answer getResource()
 * @method int getPollId()
 * @method Magento_Poll_Model_Poll_Answer setPollId(int $value)
 * @method string getAnswerTitle()
 * @method Magento_Poll_Model_Poll_Answer setAnswerTitle(string $value)
 * @method int getVotesCount()
 * @method Magento_Poll_Model_Poll_Answer setVotesCount(int $value)
 * @method int getAnswerOrder()
 * @method Magento_Poll_Model_Poll_Answer setAnswerOrder(int $value)
 *
 * @category    Magento
 * @package     Magento_Poll
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Poll_Model_Poll_Answer extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Magento_Poll_Model_Resource_Poll_Answer');
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
        Mage::getModel('Magento_Poll_Model_Poll')
            ->setId($this->getPollId())
            ->resetVotesCount();
    }

    protected function _beforeDelete()
    {
        $this->setPollId($this->load($this->getId())->getPollId());
    }

    protected function _afterDelete()
    {
        Mage::getModel('Magento_Poll_Model_Poll')
            ->setId($this->getPollId())
            ->resetVotesCount();
    }
}
