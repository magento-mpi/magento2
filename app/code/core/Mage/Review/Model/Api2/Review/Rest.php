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
 * Abstract Api2 model for review item
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Review_Model_Api2_Review_Rest extends Mage_Review_Model_Api2_Review
{
    /**
     * Get reviews collection with no filters
     *
     * @return Mage_Review_Model_Resource_Review_Collection
     */
    public function getCollection()
    {
        /** @var $collection Mage_Review_Model_Resource_Review_Collection */
        $collection = Mage::getResourceModel('review/review_collection');
        $collection->join(
            array('status' => 'review/review_status'),
            'status.status_id=main_table.status_id',
            array('status' => 'status_code')
        );
        $collection->getSelect()->columns(array('product_id' => 'main_table.entity_pk_value'));
        $collection->addStoreData();
        $collection->getSelect()->columns('detail.store_id');

        return $collection;
    }

    protected function _getProductReviews($productId)
    {
        $collection = $this->getCollection();
        $this->_applyProductFilter($collection, $productId);

        return $collection;
    }

    /**
     * Load review of the product
     *
     * @param int $productId
     * @param int $reviewId
     * @return Mage_Review_Model_Review
     */
    protected function _getReview($productId, $reviewId)
    {
        $collection = $this->_getProductReviews($productId);
        $this->_applyReviewFilter($collection, $reviewId);

        $review = $collection->getFirstItem();

        if (!$review->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        return $review;
    }

    /**
     * Apply filter by product
     *
     * @param Mage_Review_Model_Resource_Review_Collection $collection
     * @param int $productId
     * @return Mage_Review_Model_Resource_Review_Collection
     */
    protected function _applyProductFilter(Mage_Review_Model_Resource_Review_Collection $collection, $productId)
    {
        if ($productId) {
            $product = $this->_getProduct($productId);
            if (!$product->getId()) {
                $this->_critical('Invalid product', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }

            $collection->addEntityFilter(Mage_Review_Model_Review::ENTITY_PRODUCT_CODE, $product->getId());
        }

        return $collection;
    }

    protected function _getProduct($productId)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($productId);

        return $product;
    }

    /**
     * Apply filter by review
     *
     * @param Mage_Review_Model_Resource_Review_Collection $collection
     * @param int $reviewId
     */
    protected function _applyReviewFilter(Mage_Review_Model_Resource_Review_Collection $collection, $reviewId)
    {
        if ($reviewId) {
            $collection->addFieldToFilter('main_table.review_id', $reviewId);
        }
    }

    protected function _getData(Mage_Review_Model_Resource_Review_Collection $reviews)
    {
        //NOTE: this should be called after all filters applied
        //cause it internally loads collection
        $reviews->addRateVotes();

        $customerIds = array();
        foreach($reviews->load() as $review) {
            $customerId = $review->getData('customer_id');
            if (empty($customerId)) {
                continue;
            }

            $customerIds[] = $customerId;
        }

        $customersById = $this->_getCustomers($customerIds);

        $data = array();
        foreach ($reviews->load() as $review) {
            $item = $review->getData();

            $item['posted_by'] = $this->_getPostedBy($item, $customersById);
            $item['user_type'] = $this->_getUserType($item);
            $item['rating_summary'] = $this->_getRatingSummary($item);
            $item['detailed_rating'] = $this->_getDetailedRating($item);

            $data[] = $item;
        }

        return $data;
    }

    protected function _getCustomers(array $customers)
    {
        /** @var $collection Mage_Customer_Model_Resource_Customer_Collection */
        $collection = Mage::getModel('customer/customer')->getCollection();

        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS);

        $collection->addNameToSelect();
        $collection->getSelect()->columns(array('entity_id', 'email'));
        $collection->addAttributeToFilter('entity_id', array('in'=>$customers));

        $customersById = array();
        /** @var $customer Mage_Customer_Model_Customer */
        foreach ($collection as $customer) {
            $customersById[$customer->getId()] = $customer->getData();
        }

        return $customersById;
    }

    protected function _getPostedBy($item, $customersById)
    {
        //TODO what about Admin?
        $postedBy = 'Guest';

        if (isset($customersById[$item['customer_id']])) {
            $postedBy = array(
                'name'  => $customersById[$item['customer_id']]['name'],
                'email' => $customersById[$item['customer_id']]['email']
            );
        }

        return $postedBy;
    }

    protected function _getUserType($item)
    {
        $userType = null;

        if ($item['customer_id']==null) {
            if ($item['store_id'] == Mage_Core_Model_App::ADMIN_STORE_ID) {
                $userType = Mage::helper('review')->__('Administrator');
            } else {
                $userType = Mage::helper('review')->__('Guest');
            }
        } elseif ($item['customer_id'] > 0) {
            $userType = Mage::helper('review')->__('Customer');
        }

        return $userType;
    }

    protected function _getRatingSummary($item)
    {
        /** @var $rating Mage_Rating_Model_Rating */
        $rating = Mage::getModel('rating/rating');
        $ratingData = $rating->getReviewSummary($item['review_id']);

        $value = $ratingData['count'] == 0 ? '0%' : $ratingData['sum']/$ratingData['count'].'%';

        return $value;
    }

    protected function _getDetailedRating($item)
    {
        $votes = $item['rating_votes'];

        $ratingByTitle = array();
        foreach ($votes as $vote) {
            $ratingByTitle[$vote['rating_code']] = $vote['percent'].'%';
        }

        return $ratingByTitle;
    }

    protected function _getRatingOptionValue($value)
    {
        if (strpos($value, '%')!==false) {
            if (!in_array($value, array('20%','40%','60%','80%','100%'))) {
                $this->_critical(
                    Mage_Api2_Model_Resource::RESOURCE_REQUEST_DATA_INVALID,
                    sprintf('Invalid rating value "%s"', $value)
                );
            }

            $value = rtrim($value, '%');
            $value = $value / 20;
        } else {
            if (!in_array($value, array(1,2,3,4,5))) {
                $this->_critical(
                    Mage_Api2_Model_Resource::RESOURCE_REQUEST_DATA_INVALID,
                    sprintf('Invalid rating value "%s"', $value)
                );
            }
        }

        return (int)$value;
    }

    protected function _saveRatings($ratings, $review, $product)
    {
        $reviewStores = $review->getStores();

        foreach ($ratings as $code => $value) {
            /** @var $rating Mage_Rating_Model_Rating */
            $rating = Mage::getModel('rating/rating')->load($code, 'rating_code');

            $hasNoStoresToSave = true;
            foreach ($rating->getStores() as $store) {
                if (in_array($store, $reviewStores)) {
                    $hasNoStoresToSave = false;
                    break;
                }
            }

            if ($hasNoStoresToSave) {
                continue;
            }

            $optionId = $this->_getRatingOptionId($rating, $value);

            if ($optionId===null) {
                $this->_critical(Mage_Api2_Model_Resource::RESOURCE_REQUEST_DATA_INVALID);
            }

            $customerId = null;
            if ($this->getApiUser()->getType()==Mage_Api2_Model_Auth_User_Customer::USER_TYPE) {
                $customerId = $this->getApiUser()->getUserId();
            }

            $rating->setReviewId($review->getId())
                ->setCustomerId($customerId)
                ->addOptionVote($optionId, $product->getId());
        }
    }

    protected function _getRatingOptionId($rating, $value)
    {
        $options = $rating->getOptions();
        $value = $this->_getRatingOptionValue($value);

        $optionId = null;
        /** @var $option Mage_Rating_Model_Rating_Option */
        foreach ($options as $id => $option) {
            if ($option->getValue()==$value) {
                $optionId = $id;
                break;
            }
        }

        return $optionId;
    }

    /**
     * Get resource location
     *
     * @param Mage_Core_Model_Abstract $resource
     * @return string URL
     */
    protected function _getLocation($resource)
    {
        /** @var $apiTypeRoute Mage_Api2_Model_Route_ApiType */
        $apiTypeRoute = Mage::getModel('api2/route_apiType');

        $chain = $apiTypeRoute->chain(new Zend_Controller_Router_Route(
            $this->getConfig()->getRouteWithEntityTypeAction($this->getResourceType())
        ));
        $params = array(
            'api_type'   => $this->getRequest()->getApiType(),
            'id'         => $resource->getId(),
            'product_id' => $resource->getEntityPkValue()
        );
        $uri = $chain->assemble($params);

        return '/' . $uri;
    }

    protected function _checkProductEnabled($productId)
    {
        $product = $this->_getProduct($productId);
        if ($product->getStatus()!=Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
    }
}
