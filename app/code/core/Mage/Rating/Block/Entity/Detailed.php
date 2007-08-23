<?php
/**
 * Entity rating block
 *
 * @package     Mage
 * @subpackage  Rating
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Rating_Block_Entity_Detailed extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('rating/detailed.phtml');
    }

    public function toHtml()
    {
        $entityId = Mage::registry('action')->getRequest()->getParam('id');
        if( intval($entityId) <= 0 ) {
            return '';
        }

        $reviewsCount = Mage::getModel('review/review')
            ->getTotalReviews($entityId, true);
        if( $reviewsCount == 0 ) {
            return __('Be the first to review this product');
        }

        $ratingCollection = Mage::getModel('rating/rating')
            ->getResourceCollection()
            ->addEntityFilter('product') # TOFIX
            ->setPositionOrder()
            ->load();

        if ($entityId) {
            $ratingCollection->addEntitySummaryToItem($entityId);
        }

        $this->assign('collection', $ratingCollection);
        return parent::toHtml();
    }
}