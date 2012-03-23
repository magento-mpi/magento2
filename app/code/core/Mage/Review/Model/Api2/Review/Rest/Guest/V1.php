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
    protected function _create(array $data)
    {
        /** @var $helper Mage_Review_Helper_Data */
        $helper = Mage::helper('review');
        if (!$helper->getIsGuestAllowToWrite()) {
            $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED, Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED);
        }

        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->_loadProductById($this->getRequest()->getParam('product_id'));

        /** @var $validator Mage_Review_Model_Api2_Validator */
        $validator = Mage::getModel('review/api2_validator', array('resource' => $this));

        if (!$validator->isValidDataForCreateByGuest($data)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }

        $ratings = $data['detailed_rating'];
        unset($data['detailed_rating']);

        /* @var $review Mage_Review_Model_Review */
        $review = Mage::getModel('review/review');

        $review->addData($data)
            ->setStoreId(current($data['stores']))
            ->setStatusId(Mage_Review_Model_Review::STATUS_PENDING)
            ->setCustomerId(null);

        try {
            $entityId = $review->getEntityIdByCode(Mage_Review_Model_Review::ENTITY_PRODUCT_CODE);
            $review->setEntityId($entityId)
                ->setEntityPkValue($product->getId())
                ->save();

            $this->_saveRatings($ratings, $review, $product);
            $review->aggregate();
        } catch (Mage_Core_Exception $e) {
            $this->_error($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }

        return $this->_getLocation($review);
    }

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

        $collection = $this->_prepareCollection($productId);
        $this->_applyReviewFilter($collection, $reviewId);

        if ($collection->count()==0) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        $data = $this->_getData($collection);

        return current($data);
    }

    /**
     * Get list of reviews
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $productId = $this->getRequest()->getParam('product_id');

        $collection = $this->_prepareCollection($productId);
        $this->_applyCollectionModifiers($collection);

        return $this->_getData($collection);
    }

    /**
     * Get collection preapred for retrieve for guest
     *
     * @param $productId
     * @return Mage_Review_Model_Resource_Review_Collection
     */
    protected function _prepareCollection($productId)
    {
        $this->_checkProductEnabled($productId);

        $collection = $this->_getProductReviews($productId);
        $collection->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED);

        return $collection;
    }
}
