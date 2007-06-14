<?php
/**
 * Rating option model
 *
 * @package     Mage
 * @subpackage  Rating
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Rating_Model_Rating_Option extends Varien_Object 
{
    public function __construct() 
    {
        parent::__construct();
    }
    
    public function getResource()
    {
        return Mage::getSingleton('rating_resource/rating_option');
    }
    
    public function getId()
    {
        return $this->getOptionId();
    }
    
    public function load($optionId)
    {
        $this->setData($this->getResource()->load($optionId));
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
    
    public function addVote()
    {
        $this->getResource()->addVote($this);
        return $this;
    }
}
