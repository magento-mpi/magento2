<?php
/**
 * @package     Mage
 * @subpackage  Poll
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Poll_Model_Mysql4_Poll_Answer_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('poll/poll_answer');
    }

    public function addPollFilter($pollId)
    {
        $this->getSelect()->where("poll_id IN(?) ", $pollId);
        return $this;
    }

    public function countPercent($pollObject)
    {
        if( !$pollObject ) {
            return;
        } else {
            foreach( $this->getItems() as $answer ) {
                $answer->countPercent($pollObject);
            }
        }
        return $this;
    }
}