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
namespace Magento\Rating\Model;

class Observer
{
    /**
     * Cleanup product ratings after product delete
     *
     * @param   \Magento\Event\Observer $observer
     * @return  \Magento\Rating\Model\Observer
     */
    public function processProductAfterDeleteEvent(\Magento\Event\Observer $observer)
    {
        $eventProduct = $observer->getEvent()->getProduct();
        if ($eventProduct && $eventProduct->getId()) {
            \Mage::getResourceSingleton('Magento\Rating\Model\Resource\Rating')
                ->deleteAggregatedRatingsByProductId($eventProduct->getId());
        }
        return $this;
    }
}
