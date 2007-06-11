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
    public function getId()
    {
        return $this->getPollId();
    }
    
    public function getResource()
    {
        Mage::getSingleton('poll_resource/poll');
    }

    public function load($pollId)
    {
        $this->setData($this->getResource()->load($pollId));
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

    public function loadRandom()
    {
    
    }

    public function setWebsiteId($websiteId)
    {
        $this->getResource()->setWebsiteId($websiteId);
    }
    
    public function getAnswerCollection()
    {
        $collection = Mage::getModel('poll_resource/poll_answer_collection')
            ->addPollFilter($this->getId());
        return $collection;
    }
    
    public function getAnswers()
    {
        return $this->getAnswerCollection()->load();
    }
}
