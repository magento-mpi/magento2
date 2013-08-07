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
 * Review Observer Model
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Model_Observer
{
    /**
     * Add review summary info for tagged product collection
     *
     * @param Magento_Event_Observer $observer
     * @return Mage_Review_Model_Observer
     */
    public function tagProductCollectionLoadAfter(Magento_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        Mage::getSingleton('Mage_Review_Model_Review')
            ->appendSummary($collection);

        return $this;
    }

    /**
     * Cleanup product reviews after product delete
     *
     * @param   Magento_Event_Observer $observer
     * @return  Mage_Review_Model_Observer
     */
    public function processProductAfterDeleteEvent(Magento_Event_Observer $observer)
    {
        $eventProduct = $observer->getEvent()->getProduct();
        if ($eventProduct && $eventProduct->getId()) {
            Mage::getResourceSingleton('Mage_Review_Model_Resource_Review')
                ->deleteReviewsByProductId($eventProduct->getId());
        }

        return $this;
    }

    /**
     * Append review summary before rendering html
     *
     * @param Magento_Event_Observer $observer
     * @return Mage_Review_Model_Observer
     */
    public function catalogBlockProductCollectionBeforeToHtml(Magento_Event_Observer $observer)
    {
        $productCollection = $observer->getEvent()->getCollection();
        if ($productCollection instanceof Magento_Data_Collection) {
            $productCollection->load();
            Mage::getModel('Mage_Review_Model_Review')->appendSummary($productCollection);
        }

        return $this;
    }
}
