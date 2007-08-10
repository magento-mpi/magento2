<?php
/**
 * Review model
 *
 * @package     Mage
 * @subpackage  review
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Review_Model_Review extends Varien_Object
{
    public function getResource()
    {
        return Mage::getResourceSingleton('review/review');
    }

    public function getId()
    {
        return $this->getReviewId();
    }

    public function setId($reviewId)
    {
        $this->setReviewId($reviewId);
        return $this;
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
        return Mage::getResourceModel('review/review_collection');
    }

    public function getProductCollection()
    {
        return Mage::getResourceModel('review/review_product_collection');
    }

    public function getStatusCollection()
    {
        return Mage::getResourceModel('review/review_status_collection');
    }

    public function getTotalReviews($entityPkValue)
    {
        return $this->getResource()->getTotalReviews($entityPkValue);
    }
}
