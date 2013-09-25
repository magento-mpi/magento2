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

class Magento_Review_Block_View extends Magento_Catalog_Block_Product_Abstract
{
    protected $_template = 'view.phtml';

    /**
     * @var Magento_Rating_Model_Rating_Option_VoteFactory
     */
    protected $_voteFactory;

    /**
     * @var Magento_Rating_Model_RatingFactory
     */
    protected $_ratingFactory;

    /**
     * @var Magento_Review_Model_ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Model_Config $catalogConfig
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Rating_Model_Rating_Option_VoteFactory $voteFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Rating_Model_RatingFactory $ratingFactory
     * @param Magento_Review_Model_ReviewFactory $reviewFactory
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Catalog_Model_Config $catalogConfig,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Rating_Model_Rating_Option_VoteFactory $voteFactory,
        Magento_Rating_Model_RatingFactory $ratingFactory,
        Magento_Review_Model_ReviewFactory $reviewFactory,
        array $data = array()
    ) {
        $this->_voteFactory = $voteFactory;
        $this->_storeManager = $storeManager;
        $this->_reviewFactory = $reviewFactory;

        parent::__construct($storeManager, $catalogConfig, $coreRegistry, $taxData, $catalogData, $coreData,
            $context, $data);
    }

    /**
     * Retrieve current product model from registry
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProductData()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * Retrieve current review model from registry
     *
     * @return Magento_Review_Model_Review
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
     * @return Magento_Rating_Model_Resource_Rating_Option_Vote_Collection
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
        return $this->formatDate($date, Magento_Core_Model_LocaleInterface::FORMAT_TYPE_LONG);
    }
}
