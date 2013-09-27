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
 */
class Magento_Adminhtml_Block_Review_Rating_Summary extends Magento_Adminhtml_Block_Template
{
    protected $_template = 'rating/stars/summary.phtml';

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Magento_Rating_Model_Resource_Rating_Option_Vote_CollectionFactory
     */
    protected $_votesFactory;

    /**
     * @var Magento_Rating_Model_RatingFactory
     */
    protected $_ratingFactory;

    /**
     * @param Magento_Rating_Model_Resource_Rating_Option_Vote_CollectionFactory $votesFactory
     * @param Magento_Rating_Model_RatingFactory $ratingFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Rating_Model_Resource_Rating_Option_Vote_CollectionFactory $votesFactory,
        Magento_Rating_Model_RatingFactory $ratingFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_votesFactory = $votesFactory;
        $this->_ratingFactory = $ratingFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    protected function _construct()
    {
        if ($this->_coreRegistry->registry('review_data')) {
            $this->setReviewId($this->_coreRegistry->registry('review_data')->getId());
        }
    }

    public function getRating()
    {
        if (!$this->getRatingCollection()) {
            $ratingCollection = $this->_votesFactory->create()
                ->setReviewFilter($this->getReviewId())
                ->addRatingInfo()
                ->load();
            $this->setRatingCollection( ( $ratingCollection->getSize() ) ? $ratingCollection : false );
        }
        return $this->getRatingCollection();
    }

    public function getRatingSummary()
    {
        if (!$this->getRatingSummaryCache()) {
            $this->setRatingSummaryCache($this->_ratingFactory->create()
                ->getReviewSummary($this->getReviewId()));
        }

        return $this->getRatingSummaryCache();
    }
}
