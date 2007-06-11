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

    function __construct()
    {
        $this->_resource = Mage::getSingleton('poll_resource/poll');
    }

    public function loadPolls()
    {
        $this->setData($this->getResource()->loadPolls());
        return $this;    
    }

    public function loadRandom()
    {
    
    }

    public function load($poll_id)
    {
    
    }

    public function loadResults($poll_id)
    {
    
    }

    public function addVote()
    {
    
    }

    public function getResource()
    {
        return $this->_resource;
    }

    public function setWebsiteId($website_id)
    {
        $this->_resource->setWebsiteId($website_id);
    }
}

// ft:php
// fileformat:unix
// tabstop:4
