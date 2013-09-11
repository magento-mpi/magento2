<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Poll
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Poll\Model\Resource\Poll\Answer;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize collection
     */
    public function _construct()
    {
        $this->_init('\Magento\Poll\Model\Poll\Answer', '\Magento\Poll\Model\Resource\Poll\Answer');
    }

    /**
     * Add poll filter
     *
     * @param int $pollId
     * @return \Magento\Poll\Model\Resource\Poll\Answer\Collection
     */
    public function addPollFilter($pollId)
    {
        $this->getSelect()->where("poll_id IN(?) ", $pollId);
        return $this;
    }

    /**
     * Count percent
     *
     * @param \Magento\Poll\Model\Poll $pollObject
     * @return \Magento\Poll\Model\Resource\Poll\Answer\Collection
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
