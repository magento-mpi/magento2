<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Poll
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Poll_Model_Resource_Poll_Answer_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize collection
     */
    public function _construct()
    {
        $this->_init('Mage_Poll_Model_Poll_Answer', 'Mage_Poll_Model_Resource_Poll_Answer');
    }

    /**
     * Add poll filter
     *
     * @param int $pollId
     * @return Mage_Poll_Model_Resource_Poll_Answer_Collection
     */
    public function addPollFilter($pollId)
    {
        $this->getSelect()->where("poll_id IN(?) ", $pollId);
        return $this;
    }

    /**
     * Count percent
     *
     * @param Mage_Poll_Model_Poll $pollObject
     * @return Mage_Poll_Model_Resource_Poll_Answer_Collection
     */
    public function countPercent($pollObject)
    {
        if (!$pollObject) {
            return;
        } else {
            foreach ($this->getItems() as $answer) {
                $answer->countPercent($pollObject);
            }
        }
        return $this;
    }
}
