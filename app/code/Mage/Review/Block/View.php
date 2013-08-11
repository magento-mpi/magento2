<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review detailed view block
 *
 * @category   Mage
 * @package    Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Review_Block_View extends Magento_Catalog_Block_Product_Abstract
{

    protected $_template = 'view.phtml';

    /**
     * Retrieve current product model from registry
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProductData()
    {
        return Mage::registry('current_product');
    }

    /**
     * Retrieve current review model from registry
     *
     * @return Mage_Review_Model_Review
     */
    public function getReviewData()
    {
        return Mage::registry('current_review');
    }

    /**
     * Prepare link to review list for current product
     *
     * @return string
     */
    public function getBackUrl()
    {
        return Mage::getUrl('*/*/list', array('id' => $this->getProductData()->getId()));
    }

    /**
     * Retrieve collection of ratings
     *
     * @return Mage_Rating_Model_Resource_Rating_Option_Vote_Collection
     */
    public function getRating()
    {
        if( !$this->getRatingCollection() ) {
            $ratingCollection = Mage::getModel('Mage_Rating_Model_Rating_Option_Vote')
                ->getResourceCollection()
                ->setReviewFilter($this->getReviewId())
                ->setStoreFilter(Mage::app()->getStore()->getId())
                ->addRatingInfo(Mage::app()->getStore()->getId())
                ->load();
            $this->setRatingCollection( ( $ratingCollection->getSize() ) ? $ratingCollection : false );
        }
        return $this->getRatingCollection();
    }

    /**
     * Retrieve rating summary for current product
     *
     * @return string
     */
    public function getRatingSummary()
    {
        if( !$this->getRatingSummaryCache() ) {
            $this->setRatingSummaryCache(Mage::getModel('Mage_Rating_Model_Rating')->getEntitySummary($this->getProductData()->getId()));
        }
        return $this->getRatingSummaryCache();
    }

    /**
     * Retrieve total review count for current product
     *
     * @return string
     */
    public function getTotalReviews()
    {
        if( !$this->getTotalReviewsCache() ) {
            $this->setTotalReviewsCache(Mage::getModel('Mage_Review_Model_Review')->getTotalReviews($this->getProductData()->getId(), false, Mage::app()->getStore()->getId()));
        }
        return $this->getTotalReviewsCache();
    }

    /**
     * Format date in long format
     *
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, Magento_Core_Model_LocaleInterface::FORMAT_TYPE_LONG);
    }
}
