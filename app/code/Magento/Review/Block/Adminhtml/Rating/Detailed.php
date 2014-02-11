<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Block\Adminhtml\Rating;

use Magento\Rating\Model\Rating;
use Magento\Rating\Model\Rating\Option;
use Magento\Rating\Model\Resource\Rating\Collection as RatingCollection;
use Magento\Rating\Model\Resource\Rating\Option\Vote\Collection as VoteCollection;

/**
 * Adminhtml detailed rating stars
 */
class Detailed extends \Magento\Backend\Block\Template
{
    /**
     * @var VoteCollection
     */
    protected $_voteCollection = false;

    /**
     * @var string
     */
    protected $_template = 'Magento_Rating::rating/detailed.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Rating\Model\Resource\Rating\CollectionFactory
     */
    protected $_ratingsFactory;

    /**
     * @var \Magento\Rating\Model\Resource\Rating\Option\Vote\CollectionFactory
     */
    protected $_votesFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Rating\Model\Resource\Rating\CollectionFactory $ratingsFactory
     * @param \Magento\Rating\Model\Resource\Rating\Option\Vote\CollectionFactory $votesFactory
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Rating\Model\Resource\Rating\CollectionFactory $ratingsFactory,
        \Magento\Rating\Model\Resource\Rating\Option\Vote\CollectionFactory $votesFactory,
        \Magento\Registry $registry,
        array $data = array()
    ) {
        $this->_ratingsFactory = $ratingsFactory;
        $this->_votesFactory = $votesFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        if ($this->_coreRegistry->registry('review_data')) {
            $this->setReviewId($this->_coreRegistry->registry('review_data')->getReviewId());
        }
    }

    /**
     * @return RatingCollection
     */
    public function getRating()
    {
        if (!$this->getRatingCollection()) {
            if ($this->_coreRegistry->registry('review_data')) {
                $stores = $this->_coreRegistry->registry('review_data')->getStores();

                $stores = array_diff($stores, array(0));

                $ratingCollection = $this->_ratingsFactory->create()
                    ->addEntityFilter('product')
                    ->setStoreFilter($stores)
                    ->setActiveFilter(true)
                    ->setPositionOrder()
                    ->load()
                    ->addOptionToItems();

                $this->_voteCollection = $this->_votesFactory->create()
                    ->setReviewFilter($this->getReviewId())
                    ->addOptionInfo()
                    ->load()
                    ->addRatingOptions();

            } elseif (!$this->getIsIndependentMode()) {
                $ratingCollection = $this->_ratingsFactory->create()
                    ->addEntityFilter('product')
                    ->setStoreFilter(null)
                    ->setPositionOrder()
                    ->load()
                    ->addOptionToItems();
            } else {
                $stores = $this->getRequest()->getParam('select_stores') ?: $this->getRequest()->getParam('stores');
                $ratingCollection = $this->_ratingsFactory->create()
                    ->addEntityFilter('product')
                    ->setStoreFilter($stores)
                    ->setPositionOrder()
                    ->load()
                    ->addOptionToItems();
                if (intval($this->getRequest()->getParam('id'))) {
                    $this->_voteCollection = $this->_votesFactory->create()
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

    /**
     * @return $this
     */
    public function setIndependentMode()
    {
        $this->setIsIndependentMode(true);
        return $this;
    }

    /**
     * @param Option $option
     * @param Rating $rating
     * @return bool
     */
    public function isSelected($option, $rating)
    {
        if ($this->getIsIndependentMode()) {
            $ratings = $this->getRequest()->getParam('ratings');

            if (isset($ratings[$option->getRatingId()])) {
                return $option->getId() == $ratings[$option->getRatingId()];
            } elseif (!$this->_voteCollection) {
                return false;
            }
        }

        if ($this->_voteCollection) {
            foreach ($this->_voteCollection as $vote) {
                if ($option->getId() == $vote->getOptionId()) {
                    return true;
                }
            }
        }
        return false;
    }
}
