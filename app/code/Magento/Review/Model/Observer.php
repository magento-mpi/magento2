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
 * Review Observer Model
 *
 * @category   Magento
 * @package    Magento_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Review\Model;

class Observer
{
    /**
     * Add review summary info for tagged product collection
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Review\Model\Observer
     */
    public function tagProductCollectionLoadAfter(\Magento\Event\Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        \Mage::getSingleton('Magento\Review\Model\Review')
            ->appendSummary($collection);

        return $this;
    }

    /**
     * Cleanup product reviews after product delete
     *
     * @param   \Magento\Event\Observer $observer
     * @return  \Magento\Review\Model\Observer
     */
    public function processProductAfterDeleteEvent(\Magento\Event\Observer $observer)
    {
        $eventProduct = $observer->getEvent()->getProduct();
        if ($eventProduct && $eventProduct->getId()) {
            \Mage::getResourceSingleton('Magento\Review\Model\Resource\Review')
                ->deleteReviewsByProductId($eventProduct->getId());
        }

        return $this;
    }

    /**
     * Append review summary before rendering html
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Review\Model\Observer
     */
    public function catalogBlockProductCollectionBeforeToHtml(\Magento\Event\Observer $observer)
    {
        $productCollection = $observer->getEvent()->getCollection();
        if ($productCollection instanceof \Magento\Data\Collection) {
            $productCollection->load();
            \Mage::getModel('Magento\Review\Model\Review')->appendSummary($productCollection);
        }

        return $this;
    }
}
