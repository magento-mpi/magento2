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
 * Adminhtml detailed rating stars
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Review\Rating;

class Detailed extends \Magento\Adminhtml\Block\Template
{
    protected $_voteCollection = false;

    protected $_template = 'rating/detailed.phtml';

    protected function _construct()
    {
        parent::_construct();

        if( \Mage::registry('review_data') ) {
            $this->setReviewId(\Mage::registry('review_data')->getReviewId());
        }
    }

    public function getRating()
    {
        if( !$this->getRatingCollection() ) {
            if( \Mage::registry('review_data') ) {
                $stores = \Mage::registry('review_data')->getStores();

                $stores = array_diff($stores, array(0));

                $ratingCollection = \Mage::getModel('\Magento\Rating\Model\Rating')
                    ->getResourceCollection()
                    ->addEntityFilter('product')
                    ->setStoreFilter($stores)
                    ->setActiveFilter(true)
                    ->setPositionOrder()
                    ->load()
                    ->addOptionToItems();

                $this->_voteCollection = \Mage::getModel('\Magento\Rating\Model\Rating\Option\Vote')
                    ->getResourceCollection()
                    ->setReviewFilter($this->getReviewId())
                    ->addOptionInfo()
                    ->load()
                    ->addRatingOptions();

            } elseif (!$this->getIsIndependentMode()) {
                $ratingCollection = \Mage::getModel('\Magento\Rating\Model\Rating')
                    ->getResourceCollection()
                    ->addEntityFilter('product')
                    ->setStoreFilter(null)
                    ->setPositionOrder()
                    ->load()
                    ->addOptionToItems();
            } else {
                $ratingCollection = \Mage::getModel('\Magento\Rating\Model\Rating')
                    ->getResourceCollection()
                    ->addEntityFilter('product')
                    ->setStoreFilter($this->getRequest()->getParam('select_stores') ? $this->getRequest()->getParam('select_stores') : $this->getRequest()->getParam('stores'))
                    ->setPositionOrder()
                    ->load()
                    ->addOptionToItems();
                if(intval($this->getRequest()->getParam('id'))){
                    $this->_voteCollection = \Mage::getModel('\Magento\Rating\Model\Rating\Option\Vote')
                        ->getResourceCollection()
                        ->setReviewFilter(intval($this->getRequest()->getParam('id')))
                        ->addOptionInfo()
                        ->load()
                        ->addRatingOptions();
                }
            }
            $this->setRatingCollection( ( $ratingCollection->getSize() ) ? $ratingCollection : false );
        }
        return $this->getRatingCollection();
    }

    public function setIndependentMode()
    {
        $this->setIsIndependentMode(true);
        return $this;
    }

    public function isSelected($option, $rating)
    {
        if($this->getIsIndependentMode()) {
            $ratings = $this->getRequest()->getParam('ratings');

            if(isset($ratings[$option->getRatingId()])) {
                return $option->getId() == $ratings[$option->getRatingId()];
            }elseif(!$this->_voteCollection) {
                return false;
            }
        }

        if($this->_voteCollection) {
            foreach($this->_voteCollection as $vote) {
                if($option->getId() == $vote->getOptionId()) {
                    return true;
                }
            }
        }

        return false;
    }
}
