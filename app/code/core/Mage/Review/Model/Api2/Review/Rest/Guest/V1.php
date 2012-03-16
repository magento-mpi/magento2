<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Review
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 for review item
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Model_Api2_Review_Rest_Guest_V1 extends Mage_Review_Model_Api2_Review_Rest
{
    /**
     * Retrieve information about specified review item
     *
     * @throws Mage_Api2_Exception
     * @return array
     */
    protected function _retrieve()
    {
        $productId = $this->getRequest()->getParam('product_id');
        $reviewId = $this->getRequest()->getParam('id');

        $collection = $this->_prepareRetrieveCollection($productId);
        $this->_applyReviewFilter($collection, $reviewId);

        /** @var $review Mage_Review_Model_Review */
        $review = $collection->getFirstItem();

        if (!$review->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        return $review->getData();
    }

    /**
     * Get list of reviews
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $productId = $this->getRequest()->getParam('product_id');

        $collection = $this->_prepareRetrieveCollection($productId);
        $this->_applyCollectionModifiers($collection);

        $data = $collection->load()->toArray();

        return $data['items'];
    }

    /**
     * Get collection preapred for retrieve for guest
     *
     * @param $productId
     * @return Mage_Review_Model_Resource_Review_Collection
     */
    protected function _prepareRetrieveCollection($productId)
    {
        $product = $this->_getProduct($productId);
        if ($product->getStatus()!=Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        $collection = $this->_getProductReviews($productId);
        $this->_applyStatusFilter($collection, Mage_Review_Model_Review::STATUS_APPROVED);

        return $collection;
    }












    /**
     * Guest could not delete any review
     *
     * @throws Mage_Api2_Exception
     */
    protected function _delete()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Guest could not update any review
     *
     * @param array $data
     * @throws Mage_Api2_Exception
     */
    protected function _update(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Load review by its id passed through request
     *
     * @throws Mage_Api2_Exception
     * @return Mage_Review_Model_Review
     */
    protected function __loadReview()
    {
        $review = parent::_getReview();
        // check status and review store
        $storeId = $this->getRequest()->getParam('store_id');
        if ($storeId !== null) {
            $this->_validateStores(array($storeId));
            $isStoreEligible = in_array($storeId, $review->getStores());
        } else {
            $isStoreEligible = true;
        }
        $isReviewStatusEligible = ($review->getStatusId() == Mage_Review_Model_Review::STATUS_APPROVED);
        if (!$isReviewStatusEligible || !$isStoreEligible) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $review;
    }



    //TODO added

    /**
     * Create new review
     *
     * @param $data
     * @return bool
     */
    protected function _createCollection(array $data)
    {
        /** @var $reviewHelper Mage_Review_Helper_Data */
        $reviewHelper = Mage::helper('review');
        if (!$reviewHelper->getIsGuestAllowToWrite()) {
            $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
        }

        $required = array('product_id', 'nickname', 'title', 'detail');
        $notEmpty = array('product_id', 'nickname', 'title', 'detail');
        if (!isset($data['store_id']) || empty($data['store_id'])) {
            $data['store_id'] = Mage::app()->getDefaultStoreView()->getId();
        }
        $data['stores'] = array($data['store_id']);

        $this->_validateCollection($data, $required, $notEmpty);
        $data['status_id'] = Mage_Review_Model_Review::STATUS_PENDING;
        $data['customer_id'] = null;
        return parent::_createCollection($data);
    }

    /**
     * Validate status input data
     *
     * @param array $data
     * @param array $required
     * @param array $notEmpty
     */
    protected function _validateCollection(array $data, array $required = array(), array $notEmpty = array())
    {
        parent::_validate($data, $required, $notEmpty);
        $this->_validateStores($data['stores']);
    }

    /**
     * Prepare collection for retrieve
     *
     * @return Mage_Review_Model_Resource_Review_Collection
     */
    protected function __prepareRetrieveCollection()
    {
        if (!$this->getRequest()->getParam('product_id')) {
            $this->_critical('Product id is required', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }

        $collection = $this->getCollection();

        $this->_applyProductFilter($collection);
        $this->_applyCollectionModifiers($collection);
        // apply store filter
        $storeId = $this->getRequest()->getParam('store_id');
        if ($storeId) {
            $this->_validateStores(array($storeId));
            $collection->addStoreFilter($storeId);
        }
        $collection->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED);
        return $collection;
    }
}
