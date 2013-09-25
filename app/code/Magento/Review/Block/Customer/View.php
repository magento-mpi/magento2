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

class Magento_Review_Block_Customer_View extends Magento_Catalog_Block_Product_Abstract
{
    protected $_template = 'customer/view.phtml';

    /**
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Review_Model_ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * @var Magento_Rating_Model_Rating_Option_VoteFactory
     */
    protected $_voteFactory;

    /**
     * @var Magento_Rating_Model_RatingFactory
     */
    protected $_ratingFactory;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

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
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Review_Model_ReviewFactory $reviewFactory
     * @param Magento_Rating_Model_Rating_Option_VoteFactory $voteFactory
     * @param Magento_Rating_Model_RatingFactory $ratingFactory
     * @param Magento_Customer_Model_Session $customerSession
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
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Review_Model_ReviewFactory $reviewFactory,
        Magento_Rating_Model_Rating_Option_VoteFactory $voteFactory,
        Magento_Rating_Model_RatingFactory $ratingFactory,
        Magento_Customer_Model_Session $customerSession,
        array $data = array()
    ) {
        $this->_productFactory = $productFactory;
        $this->_storeManager = $storeManager;
        $this->_reviewFactory = $reviewFactory;
        $this->_voteFactory = $voteFactory;
        $this->_ratingFactory = $ratingFactory;
        $this->_customerSession = $customerSession;

        parent::__construct($storeManager, $catalogConfig, $coreRegistry, $taxData, $catalogData, $coreData, $context,
            $data);
    }


    protected function _construct()
    {
        parent::_construct();
        $this->setReviewId($this->getRequest()->getParam('id', false));
    }

    public function getProductData()
    {
        if( $this->getReviewId() && !$this->getProductCacheData() ) {
            $product = $this->_productFactory->create()
                ->setStoreId($this->_storeManager->getStore()->getId())
                ->load($this->getReviewData()->getEntityPkValue());
            $this->setProductCacheData($product);
        }
        return $this->getProductCacheData();
    }

    public function getReviewData()
    {
        if( $this->getReviewId() && !$this->getReviewCachedData() ) {
            $this->setReviewCachedData($this->_reviewFactory->create()->load($this->getReviewId()));
        }
        return $this->getReviewCachedData();
    }

    public function getBackUrl()
    {
        return $this->getUrl('review/customer');
    }

    public function getRating()
    {
        if( !$this->getRatingCollection() ) {
            $ratingCollection = $this->_voteFactory->create()
                ->getResourceCollection()
                ->setReviewFilter($this->getReviewId())
                ->addRatingInfo($this->_storeManager->getStore()->getId())
                ->setStoreFilter($this->_storeManager->getStore()->getId())
                ->load();

            $this->setRatingCollection( ( $ratingCollection->getSize() ) ? $ratingCollection : false );
        }

        return $this->getRatingCollection();
    }

    public function getRatingSummary()
    {
        if( !$this->getRatingSummaryCache() ) {
            $this->setRatingSummaryCache($this->_ratingFactory->create()->getEntitySummary($this->getProductData()->getId()));
        }
        return $this->getRatingSummaryCache();
    }

    public function getTotalReviews()
    {
        if( !$this->getTotalReviewsCache() ) {
            $this->setTotalReviewsCache($this->_reviewFactory->create()->getTotalReviews($this->getProductData()->getId()), false, $this->_storeManager->getStore()->getId());
        }
        return $this->getTotalReviewsCache();
    }

    public function dateFormat($date)
    {
        return $this->formatDate($date, Magento_Core_Model_LocaleInterface::FORMAT_TYPE_LONG);
    }

    /**
     * Check whether current customer is review owner
     *
     * @return boolean
     */
    public function isReviewOwner()
    {
        return ($this->getReviewData()->getCustomerId() == $this->_customerSession->getCustomerId());
    }
}
