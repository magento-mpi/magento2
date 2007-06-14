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
    protected $_pollId;

    public function getId()
    {
        return $this->getPollId();
    }

    public function load($pollId)
    {
        $this->getResource()->load($pollId);
    }

    public function addAnswers($pollId)
    {
        $this->getResource()->addAnswers($this);
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
    }

    public function getCollection()
    {
        return Mage::getSingleton('poll_resource/poll_collection');
    }
}