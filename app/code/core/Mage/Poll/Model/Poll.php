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

    public function load($pollId=null)
    {
        $pollId = ( isset($pollId) ) ? $pollId : $this->getId();
        $poll = $this->getResource()->load($pollId);
        $this->setPoll($poll->getPoll());
        $this->setId($pollId);
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
        return Mage::getModel('poll_resource/poll');
    }

    public function setVoted($pollId=null)
    {
        $pollId = ( isset($pollId) ) ? $pollId : $this->getId();
        Mage::getSingleton('core/cookie')->set($this->_pollCookieDefaultName . $pollId, $pollId);
        return $this;
    }

    public function isVoted($pollId=null)
    {
        $pollId = ( isset($pollId) ) ? $pollId : $this->getId();
        $cookie = Mage::getSingleton('core/cookie')->get($this->_pollCookieDefaultName . $pollId);
        if( $cookie === false ) {
            return false;
        } else {
            return true;
        }
    }

    public function loadAnswers()
    {
        $answers = Mage::getModel('poll_resource/poll_answer')->loadAnswers($this->getId());
        $this->setAnswers($answers);
        return $this;
    }

    public function calculatePercent()
    {
        $answers = $this->getAnswers();
        $answersResource = Mage::getModel('poll_resource/poll_answer');
        $poll = $this->getPoll();
        foreach( $answers as $key => $answer ) {
            #$answers[$key]['percent'] = ( $poll['votes_count'] > 0 ) ? ($answer['votes_count'] * 100 / $poll['votes_count']) : 0;
            $answers[$key]['percent'] = $answersResource->getPercent($poll['votes_count'], $answer['votes_count']);
        }
        $this->setAnswers($answers);
        return $this;
    }
}