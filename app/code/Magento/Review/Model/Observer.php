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
     * @var Magento_Review_Model_Review
     */
    protected $_review;

    /**
     * @var Magento_Review_Model_Resource_Review
     */
    protected $_reviewResource;

    /**
     * @param Magento_Review_Model_Review $review
     * @param Magento_Review_Model_Resource_Review $reviewResource
     */
    public function __construct(
        Magento_Review_Model_Review $review,
        Magento_Review_Model_Resource_Review $reviewResource
    ) {
        $this->_review = $review;
        $this->_reviewResource = $reviewResource;
    }

    /**
     * Add review summary info for tagged product collection
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Review_Model_Observer
     */
    public function tagProductCollectionLoadAfter(Magento_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        $this->_review->appendSummary($collection);

        return $this;
    }

    /**
     * Cleanup product reviews after product delete
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_Review_Model_Observer
     */
    public function processProductAfterDeleteEvent(Magento_Event_Observer $observer)
    {
        $eventProduct = $observer->getEvent()->getProduct();
        if ($eventProduct && $eventProduct->getId()) {
            $this->_reviewResource->deleteReviewsByProductId($eventProduct->getId());
        }

        return $this;
    }

    /**
     * Append review summary before rendering html
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Review_Model_Observer
     */
    public function catalogBlockProductCollectionBeforeToHtml(Magento_Event_Observer $observer)
    {
        $productCollection = $observer->getEvent()->getCollection();
        if ($productCollection instanceof Magento_Data_Collection) {
            $productCollection->load();
            Mage::getModel('Magento_Review_Model_Review')->appendSummary($productCollection);
        }

        return $this;
    }
}
