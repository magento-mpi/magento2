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
class Mage_Review_Model_Api2_Review_Rest_Customer_V1 extends Mage_Review_Model_Api2_Review_Rest
{
    /**
     * Customer could not delete any review
     *
     * @throws Mage_Api2_Exception
     */
    protected function _delete()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Customer could not update any review
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
    protected function _loadReview()
    {
        $review = parent::_loadReview();
        // check status and review store
        $storeId = $this->getRequest()->getParam('store_id');
        if ($storeId !== null) {
            $this->_validateStores(array($storeId));
            $isStoreEligible = in_array($storeId, $review->getStores());
        } else {
            $isStoreEligible = true;
        }
        if ($this->getApiUser()->getUserId() == $review->getCustomerId()) {
            // customer should be able to view his own review with any status
            $isReviewStatusEligible = true;
        } else {
            $isReviewStatusEligible = ($review->getStatusId() == Mage_Review_Model_Review::STATUS_APPROVED);
        }
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
        $required = array('product_id', 'nickname', 'title', 'detail');
        $notEmpty = array('product_id', 'nickname', 'title', 'detail');
        if (!isset($data['store_id']) || empty($data['store_id'])) {
            $data['store_id'] = Mage::app()->getDefaultStoreView()->getId();
        }
        $data['stores'] = array($data['store_id']);

        $this->_validateCollection($data, $required, $notEmpty);
        $data['status_id'] = Mage_Review_Model_Review::STATUS_PENDING;
        $data['customer_id'] = $this->getApiUser()->getUserId();

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
        //TODO fixme
        parent::_validate($data, $required, $notEmpty);
        $this->_validateStores($data['stores']);
    }

    /**
     * Prepare collection for retrieve
     *
     * @return Mage_Review_Model_Resource_Review_Collection
     */
    protected function _prepareRetrieveCollection()
    {
        $collection = $this->getCollection();

        if ($this->getRequest()->getParam('product_id')) {
            $this->_applyProductFilter($collection);
        } else {
            $this->_applyCustomerFilterCollection($collection);
        }
        $this->_applyCollectionModifiers($collection);
        // apply store filter
        $storeId = $this->getRequest()->getParam('store_id');
        if ($storeId) {
            $this->_validateStores(array($storeId));
            $collection->addStoreFilter($storeId);
        }
        return $collection;
    }

    /**
     * Apply filter by current customer
     *
     * @param Mage_Review_Model_Resource_Review_Collection $collection
     */
    protected function _applyCustomerFilterCollection(Mage_Review_Model_Resource_Review_Collection $collection)
    {
        $customerId = $this->getApiUser()->getUserId();
        if ($customerId !== null) {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('customer/customer')->load($customerId);
            if (!$customer->getId()) {
                $this->_critical('Customer not found', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            $collection->addCustomerFilter($customer->getId());
        }
    }


}
