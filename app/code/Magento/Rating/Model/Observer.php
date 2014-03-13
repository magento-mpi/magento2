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
 * Rating Observer Model
 */
class Observer
{
    /**
     * @var \Magento\Rating\Model\Resource\Rating
     */
    protected $_resourceRating;

    /**
     * @param \Magento\Rating\Model\Resource\Rating $resourceRating
     */
    public function __construct(\Magento\Rating\Model\Resource\Rating $resourceRating)
    {
        $this->_resourceRating = $resourceRating;
    }

    /**
     * Cleanup product ratings after product delete
     *
     * @param   \Magento\Event\Observer $observer
     * @return  $this
     */
    public function processProductAfterDeleteEvent(\Magento\Event\Observer $observer)
    {
        $eventProduct = $observer->getEvent()->getProduct();
        if ($eventProduct && $eventProduct->getId()) {
            $this->_resourceRating->deleteAggregatedRatingsByProductId($eventProduct->getId());
        }
        return $this;
    }
}
