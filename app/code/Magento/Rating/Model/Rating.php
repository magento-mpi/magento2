<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rating model
 *
 * @method Magento_Rating_Model_Resource_Rating getResource()
 * @method Magento_Rating_Model_Resource_Rating _getResource()
 * @method array getRatingCodes()
 * @method Magento_Rating_Model_Rating setRatingCodes(array $value)
 * @method array getStores()
 * @method Magento_Rating_Model_Rating setStores(array $value)
 * @method string getRatingCode()
 *
 * @category   Magento
 * @package    Magento_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rating_Model_Rating extends Magento_Core_Model_Abstract
{
    /**
     * rating entity codes
     */
    const ENTITY_PRODUCT_CODE           = 'product';
    const ENTITY_PRODUCT_REVIEW_CODE    = 'product_review';
    const ENTITY_REVIEW_CODE            = 'review';

    /**
     * @var Magento_Rating_Model_Rating_OptionFactory
     */
    protected $_ratingOptionFactory;

    /**
     * @var Magento_Rating_Model_Resource_Rating_Option_CollectionFactory
     */
    protected $_ratingCollectionF;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Rating_Model_Rating_OptionFactory $ratingOptionFactory
     * @param Magento_Rating_Model_Resource_Rating_Option_CollectionFactory $ratingCollectionF
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Rating_Model_Rating_OptionFactory $ratingOptionFactory,
        Magento_Rating_Model_Resource_Rating_Option_CollectionFactory $ratingCollectionF,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_ratingOptionFactory = $ratingOptionFactory;
        $this->_ratingCollectionF = $ratingCollectionF;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento_Rating_Model_Resource_Rating');
    }

    /**
     * @param int $optionId
     * @param $entityPkValue
     * @return $this
     */
    public function addOptionVote($optionId, $entityPkValue)
    {
        $this->_ratingOptionFactory->create()->setOptionId($optionId)
            ->setRatingId($this->getId())
            ->setReviewId($this->getReviewId())
            ->setEntityPkValue($entityPkValue)
            ->addVote();
        return $this;
    }

    /**
     * @param int $optionId
     * @return $this
     */
    public function updateOptionVote($optionId)
    {
        $this->_ratingOptionFactory->create()->setOptionId($optionId)
            ->setVoteId($this->getVoteId())
            ->setReviewId($this->getReviewId())
            ->setDoUpdate(1)
            ->addVote();
        return $this;
    }

    /**
     * retrieve rating options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = $this->getData('options');
        if ($options) {
            return $options;
        } elseif ($this->getId()) {
            return $this->_ratingCollectionF->create()
               ->addRatingFilter($this->getId())
               ->setPositionOrder()
               ->load()
               ->getItems();
        }
        return array();
    }

    /**
     * Get rating collection object
     *
     * @param $entityPkValue
     * @param bool $onlyForCurrentStore
     * @return Magento_Data_Collection_Db
     */
    public function getEntitySummary($entityPkValue,  $onlyForCurrentStore = true)
    {
        $this->setEntityPkValue($entityPkValue);
        return $this->_getResource()->getEntitySummary($this, $onlyForCurrentStore);
    }

    /**
     * @param $reviewId
     * @param bool $onlyForCurrentStore
     * @return array
     */
    public function getReviewSummary($reviewId,  $onlyForCurrentStore = true)
    {
        $this->setReviewId($reviewId);
        return $this->_getResource()->getReviewSummary($this, $onlyForCurrentStore);
    }

    /**
     * Get rating entity type id by code
     *
     * @param string $entityCode
     * @return int
     */
    public function getEntityIdByCode($entityCode)
    {
        return $this->getResource()->getEntityIdByCode($entityCode);
    }
}
