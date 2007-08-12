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
class Mage_Rating_Model_Rating extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        $this->_init('rating/rating');
    }

    public function addOptionVote($optionId, $entityPkValue)
    {
        Mage::getModel('rating/rating_option')->setOptionId($optionId)
            ->setRatingId($this->getId())
            ->setReviewId($this->getReviewId())
            ->setEntityPkValue($entityPkValue)
            ->addVote();
        return $this;
    }

    public function updateOptionVote($optionId)
    {
        Mage::getModel('rating/rating_option')->setOptionId($optionId)
            ->setVoteId($this->getVoteId())
            ->setReviewId($this->getReviewId())
            ->setDoUpdate(1)
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

    public function getEntitySummary($entityPkValue)
    {
        $this->setEntityPkValue($entityPkValue);
        return $this->getResource()->getEntitySummary($this);
    }

    public function getReviewSummary($reviewId)
    {
        $this->setReviewId($reviewId);
        return $this->getResource()->getReviewSummary($this);
    }
}
