<?php
/**
 * Poll
 *
 * @file        Poll.php
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski (hacki) alexander@varien.com
 */

class Mage_Poll_Model_Poll extends Varien_Object
{
    protected $_pollCookieDefaultName = 'poll';

    public function getId()
    {
        return $this->getPollId();
    }

    public function load($pollId)
    {
        $this->getResource()->load($pollId);
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

    public function setWebsiteId($websiteId)
    {
        $this->getResource()->setWebsiteId($websiteId);
        return $this;
    }

    public function getCollection()
    {
        return Mage::getModel('poll_resource/poll_collection');
    }

    public function getResource()
    {
        return Mage::getSingleton('poll_resource/poll');
    }

    public function setVoted($pollId=null)
    {
        $pollId = (is_null($pollId)) ? $pollId : $this->getId();
        Mage::getSingleton('core/cookie')->set($this->_pollCookieDefaultName . $pollId, $pollId);
        return $this;
    }

    public function  isVoted($pollId=null)
    {
        $pollId = (is_null($pollId)) ? $pollId : $this->getId();
        $cookie = Mage::getSingleton('core/cookie')->get($this->_pollCookieDefaultName . $pollId);
        if( $cookie === false ) {
            return false;
        } else {
            return true;
        }
    }
}