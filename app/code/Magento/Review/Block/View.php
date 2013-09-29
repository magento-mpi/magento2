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
 * Review detailed view block
 *
 * @category   Magento
 * @package    Magento_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Review\Block;

class View extends \Magento\Catalog\Block\Product\AbstractProduct
{
    protected $_template = 'view.phtml';

    /**
     * @var \Magento\Rating\Model\Rating\Option\VoteFactory
     */
    protected $_voteFactory;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Rating\Model\RatingFactory
     */
    protected $_ratingFactory;

    /**
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Rating\Model\Rating\Option\VoteFactory $voteFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Rating\Model\RatingFactory $ratingFactory
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Rating\Model\Rating\Option\VoteFactory $voteFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Rating\Model\RatingFactory $ratingFactory,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        array $data = array()
    ) {
        $this->_voteFactory = $voteFactory;
        $this->_storeManager = $storeManager;
        $this->_ratingFactory = $ratingFactory;
        $this->_reviewFactory = $reviewFactory;

        parent::__construct($coreRegistry, $taxData, $catalogData, $coreData, $context, $data);
    }

    /**
     * Retrieve current product model from registry
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProductData()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * Retrieve current review model from registry
     *
     * @return \Magento\Review\Model\Review
     */
    public function getReviewData()
    {
        return $this->_coreRegistry->registry('current_review');
    }

    /**
     * Prepare link to review list for current product
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/list', array('id' => $this->getProductData()->getId()));
    }

    /**
     * Retrieve collection of ratings
     *
     * @return \Magento\Rating\Model\Resource\Rating\Option\Vote\Collection
     */
    public function getRating()
    {
        if( !$this->getRatingCollection() ) {
            $ratingCollection = $this->_voteFactory->create()
                ->getResourceCollection()
                ->setReviewFilter($this->getReviewId())
                ->setStoreFilter($this->_storeManager->getStore()->getId())
                ->addRatingInfo($this->_storeManager->getStore()->getId())
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
            $this->setRatingSummaryCache(
                $this->_ratingFactory->create()->getEntitySummary($this->getProductData()->getId())
            );
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
            $this->setTotalReviewsCache(
                $this->_reviewFactory->create()->getTotalReviews(
                    $this->getProductData()->getId(), false, $this->_storeManager->getStore()->getId()
                )
            );
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
        return $this->formatDate($date, \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_LONG);
    }
}
