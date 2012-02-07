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
 * API2 for reviews collection
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Model_Api2_Reviews_Rest_Admin_V1 extends Mage_Review_Model_Api2_Reviews_Rest
{
    /**
     * Create new review
     *
     * @param $data
     * @return bool
     */
    protected function _create(array $data)
    {
        $required = array('product_id', 'status_id', 'stores', 'nickname', 'title', 'detail');
        $notEmpty = array('product_id', 'status_id', 'stores', 'nickname', 'title', 'detail');
        $this->_validate($data, $required, $notEmpty);
        $data['store_id'] = reset($data['stores']);
        $data['customer_id'] = null;

        return parent::_create($data);
    }

    /**
     * Validate status input data
     *
     * @param array $data
     * @param array $required
     * @param array $notEmpty
     */
    protected function _validate(array $data, array $required = array(), array $notEmpty = array())
    {
        parent::_validate($data, $required, $notEmpty);
        $this->_validateStatus($data['status_id']);
        $this->_validateStores($data['stores']);
    }

    /**
     * Get list of reviews
     *
     * @return array
     */
    protected function _retrieve()
    {
        /** @var $collection Mage_Review_Model_Resource_Review_Collection */
        $collection = Mage::getResourceModel('review/review_collection');
        $this->_applyProductFilter($collection);
        $this->_applyStatusFilter($collection);
        $this->_applyCollectionModifiers($collection);
        $collection->getSelect()->columns(array('product_id' => 'main_table.entity_pk_value'));
        $data = $collection->load()->toArray();

        return $data['items'];
    }

    /**
     * Apply filter by product
     *
     * @param Mage_Review_Model_Resource_Review_Collection $collection
     */
    protected function _applyProductFilter(Mage_Review_Model_Resource_Review_Collection $collection)
    {
        $productId = $this->getRequest()->getParam('product');
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
        $status = $this->getRequest()->getParam('status');
        if ($status) {
            $this->_validateStatus($status);
            $collection->addStatusFilter($status);
        }
    }
}
