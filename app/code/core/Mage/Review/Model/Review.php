<?php
/**
 * Review model
 *
 * @package     Mage
 * @subpackage  review
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Review_Model_Review extends Varien_Object 
{
    public function getResource()
    {
        return Mage::getSingleton('review_resource/review');
    }
    
    public function getId()
    {
        return $this->getReviewId();
    }
    
    public function load($reviewId)
    {
        $this->setData($this->getResource()->load($reviewId));
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
    
    public function getCollection()
    {
        return Mage::getModel('review_resource/review_collection');
    }
}
