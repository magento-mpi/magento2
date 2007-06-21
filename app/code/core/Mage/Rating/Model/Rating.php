<?php
/**
 * Rating model
 *
 * @package     Mage
 * @subpackage  Rating
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Rating_Model_Rating extends Varien_Object 
{
    public function __construct() 
    {
        parent::__construct();
    }
    
    public function getResource()
    {
        return Mage::getResourceSingleton('rating/rating');
    }
    
    public function getId()
    {
        return $this->getRatingId();
    }
    
    public function load($ratingId)
    {
        $this->setData($this->getResource()->load($ratingId));
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
    
    public function addOptionVote($optionId, $entityPkValue)
    {
        Mage::getModel('rating/rating_option')->setOptionId($optionId)
            ->setRatingId($this->getId())
            ->setEntityPkValue($entityPkValue)
            ->addVote();
        return $this;
    }
    
    /**
     * retrieve rating options
     *
     * @return array
     */
    public function getOptions()
    {
        if ($options = $this->getData('options')) {
            return $options;
        }
        elseif ($id = $this->getId()) {
        	return Mage::getResourceModel('rating/rating_option_collection')
        	   ->addRatingFilter($id)
        	   ->setPositionOrder()
        	   ->load()
        	   ->getItems();
        }
        return array();
    }
    
    /**
     * Get rating collection object
     *
     * @return Varien_Data_Collection_Db
     */
    public function getCollection()
    {
        return Mage::getResourceModel('rating/rating_collection');
    }
}
