<?php
/**
 * Entity summary rating block
 *
 * @package     Mage
 * @subpackage  Rating
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Rating_Block_Entity_Summary extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('rating/summary.phtml');
    }

    /* TO REMOVE (!!!)
    public function getRating()
    {
        if( !$this->getRatingCollection() ) {
            $ratingCollection = Mage::getModel('rating/rating_option_vote')
                ->getResourceCollection()
                ->setEntityPkFilter($this->getEntityId())
                ->addRatingInfo()
                ->load();
            $this->setRatingCollection( ( $ratingCollection->getSize() ) ? $ratingCollection : false );
        }
        return $this->getRatingCollection();
    }
    */

    public function getRatingSummary()
    {
        if( !$this->getRatingSummaryCache() ) {
            $this->setRatingSummaryCache(Mage::getModel('rating/rating')->getEntitySummary($this->getEntityId()));
        }

        return $this->getRatingSummaryCache();
    }
}