<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rating\Model;

/**
 * Rating model
 *
 * @method \Magento\Rating\Model\Resource\Rating getResource()
 * @method \Magento\Rating\Model\Resource\Rating _getResource()
 * @method array getRatingCodes()
 * @method \Magento\Rating\Model\Rating setRatingCodes(array $value)
 * @method array getStores()
 * @method \Magento\Rating\Model\Rating setStores(array $value)
 * @method string getRatingCode()
 *
 * @category   Magento
 * @package    Magento_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Rating extends \Magento\Model\AbstractModel
{
    /**
     * rating entity codes
     */
    const ENTITY_PRODUCT_CODE = 'product';

    const ENTITY_PRODUCT_REVIEW_CODE = 'product_review';

    const ENTITY_REVIEW_CODE = 'review';

    /**
     * @var \Magento\Rating\Model\Rating\OptionFactory
     */
    protected $_ratingOptionFactory;

    /**
     * @var \Magento\Rating\Model\Resource\Rating\Option\CollectionFactory
     */
    protected $_ratingCollectionF;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Rating\Model\Rating\OptionFactory $ratingOptionFactory
     * @param \Magento\Rating\Model\Resource\Rating\Option\CollectionFactory $ratingCollectionF
     * @param \Magento\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Rating\Model\Rating\OptionFactory $ratingOptionFactory,
        \Magento\Rating\Model\Resource\Rating\Option\CollectionFactory $ratingCollectionF,
        \Magento\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
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
        $this->_init('Magento\Rating\Model\Resource\Rating');
    }

    /**
     * @param int $optionId
     * @param int $entityPkValue
     * @return $this
     */
    public function addOptionVote($optionId, $entityPkValue)
    {
        $this->_ratingOptionFactory->create()->setOptionId(
            $optionId
        )->setRatingId(
            $this->getId()
        )->setReviewId(
            $this->getReviewId()
        )->setEntityPkValue(
            $entityPkValue
        )->addVote();
        return $this;
    }

    /**
     * @param int $optionId
     * @return $this
     */
    public function updateOptionVote($optionId)
    {
        $this->_ratingOptionFactory->create()->setOptionId(
            $optionId
        )->setVoteId(
            $this->getVoteId()
        )->setReviewId(
            $this->getReviewId()
        )->setDoUpdate(
            1
        )->addVote();
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
            return $this->_ratingCollectionF->create()->addRatingFilter(
                $this->getId()
            )->setPositionOrder()->load()->getItems();
        }
        return array();
    }

    /**
     * Get rating collection object
     *
     * @param int $entityPkValue
     * @param bool $onlyForCurrentStore
     * @return \Magento\Data\Collection\Db
     */
    public function getEntitySummary($entityPkValue, $onlyForCurrentStore = true)
    {
        $this->setEntityPkValue($entityPkValue);
        return $this->_getResource()->getEntitySummary($this, $onlyForCurrentStore);
    }

    /**
     * @param int $reviewId
     * @param bool $onlyForCurrentStore
     * @return array
     */
    public function getReviewSummary($reviewId, $onlyForCurrentStore = true)
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
