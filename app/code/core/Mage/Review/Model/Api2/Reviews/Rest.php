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
 * Abstract API2 class for reviews
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Review_Model_Api2_Reviews_Rest extends Mage_Review_Model_Api2_Reviews
{
    /**
     * Helper for review specific data validation
     *
     * @var Mage_Review_Model_Api2_Validator
     */
    protected $_validator;

    /**
     * Initialize validator
     */
    function __construct()
    {
        $this->_validator = Mage::getModel('review/api2_validator');
    }

    /**
     * Create new review
     *
     * @param array $data
     * @return string
     */
    protected function _create(array $data)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($data['product_id']);
        if (!$product->getId()) {
            $this->_critical('Product not found', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        /** @var $review Mage_Review_Model_Review */
        $review = Mage::getModel('review/review')->setData($data);
        try {
            $entityId = $review->getEntityIdByCode(Mage_Review_Model_Review::ENTITY_PRODUCT_CODE);
            $review->setEntityId($entityId)
                ->setEntityPkValue($product->getId())
                ->save();

        } catch (Mage_Core_Exception $e) {
            $this->_error($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }

        return $this->_getLocation($review);
    }

    /**
     * Get review location
     *
     * @param Mage_Core_Model_Abstract $review
     * @return string
     */
    protected function _getLocation(Mage_Core_Model_Abstract $review)
    {
        return $this->getConfig()->getResourceInstance('reviews') . '/' . $review->getId();
    }

    /**
     * Validate review stores input
     *
     * @throws Mage_Api2_Exception
     * @param array $stores
     */
    protected function _validateStores($stores)
    {
        if (!$this->_validator->areStoresValid($stores)) {
            $this->_critical('Invalid stores provided', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Validate review status input
     *
     * @throws Mage_Api2_Exception
     * @param int $statusId
     */
    protected function _validateStatus($statusId)
    {
        if (!$this->_validator->isStatusValid($statusId)) {
            $this->_critical('Invalid status provided', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Apply filter by product
     *
     * @param Mage_Review_Model_Resource_Review_Collection $collection
     */
    protected function _applyProductFilter(Mage_Review_Model_Resource_Review_Collection $collection)
    {
        $productId = $this->getRequest()->getParam('product_id');
        if ($productId) {
            /** @var $product Mage_Catalog_Model_Product */
            $product = Mage::getModel('catalog/product')->load($productId);
            if (!$product->getId()) {
                $this->_critical('Invalid product', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }

            $collection->addEntityFilter(Mage_Review_Model_Review::ENTITY_PRODUCT_CODE, $product->getId());
        }
    }

    /**
     * Apply filter by status
     *
     * @param Mage_Review_Model_Resource_Review_Collection $collection
     */
    protected function _applyStatusFilter(Mage_Review_Model_Resource_Review_Collection $collection)
    {
        $status = $this->getRequest()->getParam('status_id');
        if ($status) {
            $this->_validateStatus($status);
            $collection->addStatusFilter($status);
        }
    }

    /**
     * Get list of reviews
     *
     * @return array
     */
    protected function _retrieve()
    {
        $collection = $this->_prepareRetrieveCollection();
        $collection->getSelect()->columns(array('product_id' => 'main_table.entity_pk_value'));
        $data = $collection->load()->toArray();

        return $data['items'];
    }

    /**
     * Prepare collection for mass retrieval
     *
     * @return Mage_Review_Model_Resource_Review_Collection
     */
    abstract protected function _prepareRetrieveCollection();
}
