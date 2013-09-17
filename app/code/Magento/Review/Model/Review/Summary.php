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
 */
class Magento_Review_Model_Review_Summary extends Magento_Core_Model_Abstract
{
    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Review_Model_Resource_Review_Summary $resource
     * @param Magento_Review_Model_Resource_Review_Summary_Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Review_Model_Resource_Review_Summary $resource,
        Magento_Review_Model_Resource_Review_Summary_Collection $resourceCollection,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
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
