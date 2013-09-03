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
class Magento_Review_Model_Observer
{
    /**
     * Add review summary info for tagged product collection
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_Review_Model_Observer
     */
    public function tagProductCollectionLoadAfter(\Magento\Event\Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        Mage::getSingleton('Magento_Review_Model_Review')
            ->appendSummary($collection);

        return $this;
    }

    /**
     * Cleanup product reviews after product delete
     *
     * @param   \Magento\Event\Observer $observer
     * @return  Magento_Review_Model_Observer
     */
    public function processProductAfterDeleteEvent(\Magento\Event\Observer $observer)
    {
        $eventProduct = $observer->getEvent()->getProduct();
        if ($eventProduct && $eventProduct->getId()) {
            Mage::getResourceSingleton('Magento_Review_Model_Resource_Review')
                ->deleteReviewsByProductId($eventProduct->getId());
        }

        return $this;
    }

    /**
     * Append review summary before rendering html
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_Review_Model_Observer
     */
    public function catalogBlockProductCollectionBeforeToHtml(\Magento\Event\Observer $observer)
    {
        $productCollection = $observer->getEvent()->getCollection();
        if ($productCollection instanceof \Magento\Data\Collection) {
            $productCollection->load();
            Mage::getModel('Magento_Review_Model_Review')->appendSummary($productCollection);
        }

        return $this;
    }
}
