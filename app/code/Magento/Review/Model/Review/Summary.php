<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Model\Review;

/**
 * Review summary
 */
class Summary extends \Magento\Core\Model\AbstractModel
{
    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Review\Model\Resource\Review\Summary $resource
     * @param \Magento\Review\Model\Resource\Review\Summary\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Review\Model\Resource\Review\Summary $resource,
        \Magento\Review\Model\Resource\Review\Summary\Collection $resourceCollection,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return int
     */
    public function getEntityPkValue()
    {
        return $this->_getData('entity_pk_value');
    }

    /**
     * @return string
     */
    public function getRatingSummary()
    {
        return $this->_getData('rating_summary');
    }

    /**
     * @return int
     */
    public function getReviewsCount()
    {
        return $this->_getData('reviews_count');
    }
}
