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
 *
 * @category   Magento
 * @package    Magento_Rating
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rating_Model_Observer
{
    /**
     * @var Magento_Rating_Model_Resource_Rating
     */
    protected $_rating;

    /**
     * @param Magento_Rating_Model_Resource_Rating $rating
     */
    public function __construct(Magento_Rating_Model_Resource_Rating $rating)
    {
        $this->_rating = $rating;
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
            $this->_rating->deleteAggregatedRatingsByProductId($eventProduct->getId());
        }
        return $this;
    }
}
