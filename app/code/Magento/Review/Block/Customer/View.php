<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Review detailed view block
 *
 * @category   Magento
 * @package    Magento_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Review\Block\Customer;

class View extends \Magento\Catalog\Block\Product\AbstractProduct
{

    protected $_template = 'customer/view.phtml';

    protected function _construct()
    {
        parent::_construct();


        $this->setReviewId($this->getRequest()->getParam('id', false));
    }

    public function getProductData()
    {
        if( $this->getReviewId() && !$this->getProductCacheData() ) {
            $product = \Mage::getModel('\Magento\Catalog\Model\Product')
                ->setStoreId(\Mage::app()->getStore()->getId())
                ->load($this->getReviewData()->getEntityPkValue());
            $this->setProductCacheData($product);
        }
        return $this->getProductCacheData();
    }

    public function getReviewData()
    {
        if( $this->getReviewId() && !$this->getReviewCachedData() ) {
            $this->setReviewCachedData(\Mage::getModel('\Magento\Review\Model\Review')->load($this->getReviewId()));
        }
        return $this->getReviewCachedData();
    }

    public function getBackUrl()
    {
        return \Mage::getUrl('review/customer');
    }

    public function getRating()
    {
        if( !$this->getRatingCollection() ) {
            $ratingCollection = \Mage::getModel('\Magento\Rating\Model\Rating\Option\Vote')
                ->getResourceCollection()
                ->setReviewFilter($this->getReviewId())
                ->addRatingInfo(\Mage::app()->getStore()->getId())
                ->setStoreFilter(\Mage::app()->getStore()->getId())
                ->load();

            $this->setRatingCollection( ( $ratingCollection->getSize() ) ? $ratingCollection : false );
        }

        return $this->getRatingCollection();
    }

    public function getRatingSummary()
    {
        if( !$this->getRatingSummaryCache() ) {
            $this->setRatingSummaryCache(\Mage::getModel('\Magento\Rating\Model\Rating')->getEntitySummary($this->getProductData()->getId()));
        }
        return $this->getRatingSummaryCache();
    }

    public function getTotalReviews()
    {
        if( !$this->getTotalReviewsCache() ) {
            $this->setTotalReviewsCache(\Mage::getModel('\Magento\Review\Model\Review')->getTotalReviews($this->getProductData()->getId()), false, \Mage::app()->getStore()->getId());
        }
        return $this->getTotalReviewsCache();
    }

    public function dateFormat($date)
    {
        return $this->formatDate($date, \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_LONG);
    }

    /**
     * Check whether current customer is review owner
     *
     * @return boolean
     */
    public function isReviewOwner()
    {
        return ($this->getReviewData()->getCustomerId() == \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId());
    }
}
