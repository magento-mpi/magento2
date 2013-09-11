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
 * Poll vote resource model
 *
 * @category    Magento
 * @package     Magento_Poll
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Poll\Model\Resource\Poll;

class Vote extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize vote resource
     *
     */
    protected function _construct()
    {
        $this->_init('poll_vote', 'vote_id');
    }

    /**
     * Perform actions after object save
     *
     * @param \Magento\Object $object
     * @return \Magento\Poll\Model\Resource\Poll\Vote
     */
    protected function _afterSave(\Magento\Core\Model\AbstractModel $object)
    {
        /**
         * Increase answer votes
         */
        $answerTable = $this->getTable('poll_answer');
        $pollAnswerData = array('votes_count' => new \Zend_Db_Expr('votes_count+1'));
        $condition = array("{$answerTable}.answer_id=?" => $object->getPollAnswerId());
        $this->_getWriteAdapter()->update($answerTable, $pollAnswerData, $condition);

        /**
         * Increase poll votes
         */
        $pollTable = $this->getTable('poll');
        $pollData = array('votes_count' => new \Zend_Db_Expr('votes_count+1'));
        $condition = array("{$pollTable}.poll_id=?" => $object->getPollId());
        $this->_getWriteAdapter()->update($pollTable, $pollData, $condition);
        return $this;
    }
}
