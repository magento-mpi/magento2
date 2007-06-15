<?php
/**
 * Resource for poll answers
 *
 * @package     Mage
 * @subpackage  Poll
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Poll_Model_Poll_Answer extends Varien_Object
{
    protected $_answer;

    public function __construct($answerId=null)
    {
        if( isset($answerId) ) {
            $this->setId($answerId);
            $this->load();
        }
        return $this;
    }

    public function load($answerId=null)
    {
        if( isset($answerId) ) {
            $this->setId($answerId);
        }
        $this->_answer = $this->getResource()->load();
        return $this;
    }

    public function save()
    {
        $this->getResource()->save($this);
        return $this;
    }

    public function delete()
    {
        $this->getResource()->delete($this);
        return $this;
    }

    public function getResource()
    {
        return Mage::getSingleton('poll_resource/answer');
    }

    protected function setId($id)
    {
        $this->getResource()->setId($id);
        return $this;
    }
}