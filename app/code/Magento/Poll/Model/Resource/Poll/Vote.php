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
class Magento_Poll_Model_Resource_Poll_Vote extends Magento_Core_Model_Resource_Db_Abstract
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
     * @param Magento_Object $object
     * @return Magento_Poll_Model_Resource_Poll_Vote
     */
    protected function _afterSave(Magento_Core_Model_Abstract $object)
    {
        /**
         * Increase answer votes
         */
        $answerTable = $this->getTable('poll_answer');
        $pollAnswerData = array('votes_count' => new Zend_Db_Expr('votes_count+1'));
        $condition = array("{$answerTable}.answer_id=?" => $object->getPollAnswerId());
        $this->_getWriteAdapter()->update($answerTable, $pollAnswerData, $condition);

        /**
         * Increase poll votes
         */
        $pollTable = $this->getTable('poll');
        $pollData = array('votes_count' => new Zend_Db_Expr('votes_count+1'));
        $condition = array("{$pollTable}.poll_id=?" => $object->getPollId());
        $this->_getWriteAdapter()->update($pollTable, $pollData, $condition);
        return $this;
    }
}
