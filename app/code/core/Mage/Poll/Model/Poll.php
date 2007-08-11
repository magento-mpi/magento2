<?php
/**
 * Poll model
 *
 * @file        Poll.php
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski (hacki) alexander@varien.com
 */

class Mage_Poll_Model_Poll extends Mage_Core_Model_Abstract
{
    protected $_pollCookieDefaultName = 'poll';

    protected function _construct()
    {
        $this->_init('poll/poll');
    }

    public function resetVotesCount()
    {
        $this->getResource()->resetVotesCount($this);
        return $this;
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

    public function getRandomId()
    {
        return $this->getResource()->getRandomId($this);
    }

    public function getVotedPollsIds()
    {
        $idsArray = array();
        foreach( $_COOKIE as $cookieName => $cookieValue ) {
            $pattern = "/^" . $this->_pollCookieDefaultName . "([0-9]*?)$/";
            if( preg_match($pattern, $cookieName, $m) ) {
                if( $m[1] != Mage::getSingleton('core/session')->getJustVotedPoll() ) {
                    $idsArray[$m[1]] = $m[1];
                }
            }
        }
        return $idsArray;
    }
}