<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Rating
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rating votes collection
 *
 * @category   Mage
 * @package    Mage_Rating
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

    public function setStoreFilter($storeId)
    {
        $this->_sqlSelect->join(array('store'=>$this->getTable('review/review_store')), 'main_table.review_id=store.review_id AND store.store_id=' . (int)$storeId, array());
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