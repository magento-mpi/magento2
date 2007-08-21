<?php
/**
 * Rating votes collection
 *
 * @package     Mage
 * @subpackage  Rating
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Rating_Model_Mysql4_Rating_Option_Vote_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('rating/rating_option_vote');
    }

    public function setReviewFilter($reviewId)
    {
        $this->_sqlSelect->where("main_table.review_id = ?", $reviewId);
        return $this;
    }

    public function setEntityPkFilter($entityId)
    {
        $this->_sqlSelect->where("entity_pk_value = ?", $entityId);
        return $this;
    }

    public function addRatingInfo()
    {
        $this->_sqlSelect->join($this->getTable('rating/rating'), "{$this->getTable('rating/rating')}.rating_id = main_table.rating_id", "{$this->getTable('rating/rating')}.*");
        return $this;
    }

    public function addOptionInfo()
    {
        $this->_sqlSelect->join($this->getTable('rating/rating_option'), "main_table.option_id = {$this->getTable('rating/rating_option')}.option_id", "{$this->getTable('rating/rating_option')}.*");
        return $this;
    }

    public function addRatingOptions()
    {
        if( !$this->getSize() ) {
            return $this;
        }
        foreach( $this->getItems() as $item ) {
            $options = Mage::getModel('rating/rating_option')
                    ->getResourceCollection()
                    ->addRatingFilter($item->getRatingId())
                    ->load();

            if( $item->getRatingId() ) {
                $item->setRatingOptions($options);
            } else {
                return;
            }
        }
        return $this;
    }
}