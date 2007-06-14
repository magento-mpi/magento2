<?php
/**
 * Vote model
 *
 * @package     Mage
 * @subpackage  Poll
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 * @TODO        Add vote params ['ip_address', 'customer_id']
 */
class Mage_Poll_Model_Poll_Vote extends Varien_Object
{
    protected $_pollId;
    protected $_resource;

    public function getId()
    {
        return $this->getPollId();
    }

    public function addVote($answerId)
    {
        $pollParams = array(
                        'ip_address' => ip2long($this->getIpAddress()),
                        'poll_answer_id' => $answerId,
                        'poll_id' => $this->getPollId(),
                        'customer_id' => $this->getCustomerId()
                    );
        $this->_getResource()->add($pollParams);
    }

    protected function _getResource()
    {
        if (!$this->_resource) {
        	$this->_resource = Mage::getSingleton('poll_resource/poll_answer_vote');
        }
        return $this->_resource;
    }
}