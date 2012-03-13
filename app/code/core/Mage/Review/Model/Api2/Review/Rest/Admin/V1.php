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
class Mage_Review_Model_Api2_Review_Rest_Admin_V1 extends Mage_Review_Model_Api2_Review_Rest
{
    /**
     * Delete specified review item
     *
     * @throws Mage_Api2_Exception
     */
    protected function _delete()
    {
        $review = $this->_loadReview();
        try {
            $review->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_error($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }
    }

    /**
     * Update specified review item
     *
     * @param array $data
     * @throws Mage_Api2_Exception
     */
    protected function _update(array $data)
    {
        $notEmpty = array('status_id', 'stores', 'nickname', 'title', 'detail');
        $this->_validate($data, array(), $notEmpty);

        $review = $this->_loadReview();
        $review->addData($data);
        if (isset($data['stores'])) {
            $review->setStores($data['stores']);
        }
        try {
            $review->save();
        } catch (Mage_Core_Exception $e) {
            $this->_error($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }
    }

    /**
     * Validate stores including admin area
     *
     * @param mixed $stores
     */
    protected function _validateStores($stores)
    {
        if (!$this->_validator->areStoresValid($stores, true)) {
            $this->_critical('Invalid stores provided', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
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
        $required = array('product_id', 'status_id', 'stores', 'nickname', 'title', 'detail');
        $notEmpty = array('product_id', 'status_id', 'stores', 'nickname', 'title', 'detail');
        $this->_validateCollection($data, $required, $notEmpty);
        $data['store_id'] = reset($data['stores']);
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
        //TODO fixme
        parent::_validate($data, $required, $notEmpty);
        $this->_validateStatus($data['status_id']);
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

        $this->_applyProductFilter($collection);
        $this->_applyStatusFilter($collection);
        $this->_applyCustomerFilterCollection($collection);
        $this->_applyCollectionModifiers($collection);
        return $collection;
    }

    /**
     * Apply filter by current customer
     *
     * @param Mage_Review_Model_Resource_Review_Collection $collection
     */
    protected function _applyCustomerFilterCollection(Mage_Review_Model_Resource_Review_Collection $collection)
    {
        $customerId = $this->getRequest()->getParam('customer_id');
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
