<?php
/**
 * Vote model
 *
 * @package     Mage
 * @subpackage  Poll
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Poll_Model_Poll_Vote extends Varien_Object
{
    protected $_pollId;
    protected $_resource;

    public function getId()
    {
        return $this->getPollId();
    }

    public function addVote()
    {
        $this->_getResource()->add($this);
    }

    protected function _getResource()
    {
        if (!$this->_resource) {
        	$this->_resource = Mage::getSingleton('poll_resource/poll_answer_vote');
        }
        return $this->_resource;
    }
}