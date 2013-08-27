<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Poll
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Poll_Model_Resource_Poll_Answer_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize collection
     */
    public function _construct()
    {
        $this->_init('Magento_Poll_Model_Poll_Answer', 'Magento_Poll_Model_Resource_Poll_Answer');
    }

    /**
     * Add poll filter
     *
     * @param int $pollId
     * @return Magento_Poll_Model_Resource_Poll_Answer_Collection
     */
    public function addPollFilter($pollId)
    {
        $this->getSelect()->where("poll_id IN(?) ", $pollId);
        return $this;
    }

    /**
     * Count percent
     *
     * @param Magento_Poll_Model_Poll $pollObject
     * @return Magento_Poll_Model_Resource_Poll_Answer_Collection
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
