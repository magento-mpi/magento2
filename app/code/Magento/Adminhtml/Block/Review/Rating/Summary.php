<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml summary rating stars
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Review\Rating;

class Summary extends \Magento\Adminhtml\Block\Template
{
    protected $_template = 'rating/stars/summary.phtml';

    protected function _construct()
    {
        if (\Mage::registry('review_data')) {
            $this->setReviewId(\Mage::registry('review_data')->getId());
        }
    }

    public function getRating()
    {
        if( !$this->getRatingCollection() ) {
            $ratingCollection = \Mage::getModel('\Magento\Rating\Model\Rating\Option\Vote')
                ->getResourceCollection()
                ->setReviewFilter($this->getReviewId())
                ->addRatingInfo()
                ->load();
            $this->setRatingCollection( ( $ratingCollection->getSize() ) ? $ratingCollection : false );
        }
        return $this->getRatingCollection();
    }

    public function getRatingSummary()
    {
        if( !$this->getRatingSummaryCache() ) {
            $this->setRatingSummaryCache(\Mage::getModel('\Magento\Rating\Model\Rating')->getReviewSummary($this->getReviewId()));
        }

        return $this->getRatingSummaryCache();
    }
}
