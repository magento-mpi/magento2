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
 * Review model
 *
 * @method \Magento\Review\Model\Resource\Review _getResource()
 * @method \Magento\Review\Model\Resource\Review getResource()
 * @method string getCreatedAt()
 * @method \Magento\Review\Model\Review setCreatedAt(string $value)
 * @method \Magento\Review\Model\Review setEntityId(int $value)
 * @method int getEntityPkValue()
 * @method \Magento\Review\Model\Review setEntityPkValue(int $value)
 * @method int getStatusId()
 * @method \Magento\Review\Model\Review setStatusId(int $value)
 *
 * @category    Magento
 * @package     Magento_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Review\Model;

class Review extends \Magento\Core\Model\AbstractModel
{

    /**
     * Event prefix for observer
     *
     * @var string
     */
    protected $_eventPrefix = 'review';

    /**
     * Review entity codes
     *
     */
    const ENTITY_PRODUCT_CODE   = 'product';
    const ENTITY_CUSTOMER_CODE  = 'customer';
    const ENTITY_CATEGORY_CODE  = 'category';

    const STATUS_APPROVED       = 1;
    const STATUS_PENDING        = 2;
    const STATUS_NOT_APPROVED   = 3;

    protected function _construct()
    {
        $this->_init('\Magento\Review\Model\Resource\Review');
    }

    public function getProductCollection()
    {
        return \Mage::getResourceModel('Magento\Review\Model\Resource\Review\Product\Collection');
    }

    public function getStatusCollection()
    {
        return \Mage::getResourceModel('Magento\Review\Model\Resource\Review\Status\Collection');
    }

    public function getTotalReviews($entityPkValue, $approvedOnly=false, $storeId=0)
    {
        return $this->getResource()->getTotalReviews($entityPkValue, $approvedOnly, $storeId);
    }

    public function aggregate()
    {
        $this->getResource()->aggregate($this);
        return $this;
    }

    public function getEntitySummary($product, $storeId=0)
    {
        $summaryData = \Mage::getModel('Magento\Review\Model\Review\Summary')
            ->setStoreId($storeId)
            ->load($product->getId());
        $summary = new \Magento\Object();
        $summary->setData($summaryData->getData());
        $product->setRatingSummary($summary);
    }

    public function getPendingStatus()
    {
        return self::STATUS_PENDING;
    }

    public function getReviewUrl()
    {
        return \Mage::getUrl('review/product/view', array('id' => $this->getReviewId()));
    }

    public function validate()
    {
        $errors = array();

        if (!Zend_Validate::is($this->getTitle(), 'NotEmpty')) {
            $errors[] = __('The review summary field can\'t be empty.');
        }

        if (!Zend_Validate::is($this->getNickname(), 'NotEmpty')) {
            $errors[] = __('The nickname field can\'t be empty.');
        }

        if (!Zend_Validate::is($this->getDetail(), 'NotEmpty')) {
            $errors[] = __('The review field can\'t be empty.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Perform actions after object delete
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _afterDeleteCommit()
    {
        $this->getResource()->afterDeleteCommit($this);
        return parent::_afterDeleteCommit();
    }

    /**
     * Append review summary to product collection
     *
     * @param \Magento\Catalog\Model\Resource\Product\Collection $collection
     * @return \Magento\Review\Model\Review
     */
    public function appendSummary($collection)
    {
        $entityIds = array();
        foreach ($collection->getItems() as $_itemId => $_item) {
            $entityIds[] = $_item->getEntityId();
        }

        if (sizeof($entityIds) == 0) {
            return $this;
        }

        $summaryData = \Mage::getResourceModel('Magento\Review\Model\Resource\Review\Summary\Collection')
            ->addEntityFilter($entityIds)
            ->addStoreFilter(\Mage::app()->getStore()->getId())
            ->load();

        foreach ($collection->getItems() as $_item ) {
            foreach ($summaryData as $_summary) {
                if ($_summary->getEntityPkValue() == $_item->getEntityId()) {
                    $_item->setRatingSummary($_summary);
                }
            }
        }

        return $this;
    }

    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }

    /**
     * Check if current review approved or not
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->getStatusId() == self::STATUS_APPROVED;
    }

    /**
     * Check if current review available on passed store
     *
     * @param int|\Magento\Core\Model\Store $store
     * @return bool
     */
    public function isAvailableOnStore($store = null)
    {
        $store = \Mage::app()->getStore($store);
        if ($store) {
            return in_array($store->getId(), (array)$this->getStores());
        }

        return false;
    }

    /**
     * Get review entity type id by code
     *
     * @param string $entityCode
     * @return int|bool
     */
    public function getEntityIdByCode($entityCode)
    {
        return $this->getResource()->getEntityIdByCode($entityCode);
    }
}
