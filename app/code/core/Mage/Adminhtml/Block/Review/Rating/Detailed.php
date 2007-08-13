<?php
/**
 * Adminhtml detailed rating stars
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Review_Rating_Detailed extends Mage_Core_Block_Template
{
    public function __construct()
    {
        $this->setTemplate('rating/detailed.phtml');
        if( Mage::registry('review_data') ) {
            $this->setReviewId(Mage::registry('review_data')->getId());
        }
    }

    public function getRating()
    {
        if( !$this->getRatingCollection() ) {
            if( Mage::registry('review_data') ) {
                $ratingCollection = Mage::getModel('rating/rating_option_vote')
                    ->getResourceCollection()
                    ->setReviewFilter($this->getReviewId())
                    ->addRatingInfo()
                    ->addOptionInfo()
                    ->load()
                    ->addRatingOptions();
            } else {
                $ratingCollection = Mage::getModel('rating/rating')
                    ->getResourceCollection()
                    ->addEntityFilter('product')
                    ->setPositionOrder()
                    ->load()
                    ->addOptionToItems();
            }

            $this->setRatingCollection( ( $ratingCollection->getSize() ) ? $ratingCollection : false );
        }
        return $this->getRatingCollection();
    }
}