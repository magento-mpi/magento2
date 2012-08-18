<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review summary
 *
 * @category   Mage
 * @package    Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Review_Model_Review_Summary extends Mage_Core_Model_Abstract
{
    /**
     * @param Mage_Core_Model_Event_Manager $eventDispatcher
     * @param Mage_Core_Model_Cache $cacheManager
     * @param Mage_Review_Model_Resource_Review_Summary $resource
     * @param Mage_Review_Model_Resource_Review_Summary $resourceCollection
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Event_Manager $eventDispatcher,
        Mage_Core_Model_Cache $cacheManager,
        Mage_Review_Model_Resource_Review_Summary $resource,
        Mage_Review_Model_Resource_Review_Summary $resourceCollection,
        array $data = array()
    ) {
        parent::__construct($eventDispatcher, $cacheManager, $resource, $resourceCollection, $data);
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
