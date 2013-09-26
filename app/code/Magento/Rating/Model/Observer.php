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
 * Rating Observer Model
 */
class Magento_Rating_Model_Observer
{
    /**
     * @var Magento_Rating_Model_Resource_Rating
     */
    protected $_resourceRating;

    /**
     * @param Magento_Rating_Model_Resource_Rating $resourceRating
     */
    public function __construct(Magento_Rating_Model_Resource_Rating $resourceRating)
    {
        $this->_resourceRating = $resourceRating;
    }

    /**
     * Cleanup product ratings after product delete
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_Rating_Model_Observer
     */
    public function processProductAfterDeleteEvent(Magento_Event_Observer $observer)
    {
        $eventProduct = $observer->getEvent()->getProduct();
        if ($eventProduct && $eventProduct->getId()) {
            $this->_resourceRating->deleteAggregatedRatingsByProductId($eventProduct->getId());
        }
        return $this;
    }
}
