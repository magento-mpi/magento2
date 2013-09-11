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
 * Review summary
 *
 * @category   Magento
 * @package    Magento_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Review\Model\Review;

class Summary extends \Magento\Core\Model\AbstractModel
{
    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Review\Model\Resource\Review\Summary $resource
     * @param \Magento\Review\Model\Resource\Review\Summary\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Review\Model\Resource\Review\Summary $resource,
        \Magento\Review\Model\Resource\Review\Summary\Collection $resourceCollection,
        array $data = array()
    ) {
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    public function getEntityPkValue()
    {
        return $this->_getData('entity_pk_value');
    }

    public function getRatingSummary()
    {
        return $this->_getData('rating_summary');
    }

    public function getReviewsCount()
    {
        return $this->_getData('reviews_count');
    }

}
