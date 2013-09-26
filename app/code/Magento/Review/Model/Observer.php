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
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * @var \Magento\Review\Model\Resource\Review
     */
    protected $_resourceReview;

    /**
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param \Magento\Review\Model\Resource\Review $resourceReview
     */
    public function __construct(
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Model\Resource\Review $resourceReview
    ) {
        $this->_reviewFactory = $reviewFactory;
        $this->_resourceReview = $resourceReview;
    }
    /**
     * Add review summary info for tagged product collection
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Review\Model\Observer
     */
    public function tagProductCollectionLoadAfter(\Magento\Event\Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        $this->_reviewFactory->create()->appendSummary($collection);

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
            $this->_resourceReview->deleteReviewsByProductId($eventProduct->getId());
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
            $this->_reviewFactory->create()->appendSummary($productCollection);
        }

        return $this;
    }
}
