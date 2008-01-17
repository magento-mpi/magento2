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
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * LoadTest Renderer Reviews and Rating model
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @author     Victor Tihonchuk <victor@varien.com>
 */

class Mage_LoadTest_Model_Renderer_Review extends Mage_LoadTest_Model_Renderer_Abstract
{
    /**
     * Ratings collection
     *
     * @var array
     */
    protected $_ratings;

    /**
     * Customers collection
     *
     * @var array
     */
    protected $_customers;

    /**
     * Products collection
     *
     * @var array
     */
    protected $_products;

    /**
     * Stores collection
     *
     * @var array
     */
    protected $_stores;

    /**
     * Store Ids array
     *
     * @var array
     */
    protected $_storeIds;

    /**
     * Review titles data
     *
     * @var array
     */
    protected $_reviewsData;

    /**
     * Processed reviews
     *
     * @var array
     */
    public $reviews;

    /**
     * Init model
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->setMinCount(50);
        $this->setMaxCount(250);
    }

    /**
     * Render Reviews and Ratings
     *
     * @return Mage_LoadTest_Model_Renderer_Review
     */
    public function render()
    {
        $this->reviews = array();
        for ($i = 0; $i < rand($this->getMinCount(), $this->getMaxCount()); $i++) {
            $this->_createReview();
        }

        return $this;
    }

    /**
     * Delete all reviews and ratings
     *
     * @return Mage_LoadTest_Model_Renderer_Review
     */
    public function delete()
    {
        $this->_loadData();

        $this->reviews = array();
        $collection = Mage::getModel('review/review')
            ->getCollection()
            ->load();

        foreach ($collection as $review) {
            $this->_beforeUsedMemory();

            if (isset($this->_customers[intval($review->getCustomerId())])) {
                $customer = $this->_customers[intval($review->getCustomerId())];
                $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
            }
            else {
                $customerName = Mage::helper('loadtest')->__('Guest');
            }
            $this->reviews[$review->getId()] = array(
                'customer_id'   => $review->getCustomerId(),
                'customer_name' => $customerName,
                'product_id'    => $review->getEntityPkValue(),
                'product_name'  => $this->_products[$review->getEntityPkValue()]->getNmae(),
                'review_title'  => $review->getTitle()
            );
            $review->delete();

            $this->_afterUsedMemory();
        }

        return $this;
    }

    /**
     * Create review and set rating
     *
     * @return int
     */
    protected function _createReview()
    {
        $this->_loadData();

        $this->_beforeUsedMemory();

        $product = $this->_products[array_rand($this->_products)];
        $customer = $this->_customers[array_rand($this->_customers)];

        $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
        $reviewTitle = trim($this->_reviewsData[array_rand($this->_reviewsData)]);
        $reviewDetail = $reviewTitle;

        $review = Mage::getModel('review/review');
        $review->setEntityId(1) // product
            ->setNickname($customerName)
            ->setTitle($reviewTitle)
            ->setDetail($reviewDetail)
            ->setEntityPkValue($product->getId())
            ->setStatusId(1) // approved
            ->setCustomerId($customer->getId())
            ->setStoreId($customer->getStoreId())
            ->setStores($this->_storeIds)
            ->save();

        $ratings = array();
        foreach ($this->_ratings as $item) {
            if (in_array($customer->getStoreId(), $item->getStores())) {
                $optionIds = $item->getOptions();
                $ratings[$item->getId()] = $optionIds[array_rand($optionIds)];
            }
        }

        foreach ($ratings as $ratingId => $optionId) {
            Mage::getModel('rating/rating')
                ->setRatingId($ratingId)
                ->setReviewId($review->getId())
                ->setCustomerId($customer->getId())
                ->addOptionVote($optionId, $product->getId());
        }

        $review->aggregate();

        $reviewId = $review->getId();
        $this->reviews[$reviewId] = array(
            'customer_id'   => $customer->getId(),
            'customer_name' => $customerName,
            'product_id'    => $product->getId(),
            'product_name'  => $product->getName(),
            'review_title'  => $reviewTitle
        );

        unset($review);

        $this->_afterUsedMemory();

        return $reviewId;
    }

    /**
     * Load model data
     *
     */
    protected function _loadData()
    {
        if (is_null($this->_ratings)) {
            $collection = Mage::getModel('rating/rating')
                ->getCollection()
                ->load();
            $collection->addStoresToCollection();
            foreach ($collection as $rating) {
                $optionsCollection = Mage::getModel('rating/rating_option')
                    ->getCollection()
                    ->addRatingFilter($rating->getId())
                    ->load();
                $optionIds = array();
                foreach ($optionsCollection as $option) {
                    $optionIds[] = $option->getOptionId();
                }
                $rating->setOptions($optionIds);
            }
            $this->_ratings = $collection;
        }
        if (is_null($this->_customers)) {
            $collection = Mage::getModel('customer/customer')
                ->getCollection()
                ->addAttributeToSelect('firstname')
                ->addAttributeToSelect('lastname')
                ->load();
            $this->_customers = array();
            foreach ($collection as $customer) {
                $this->_customers[$customer->getId()] = $customer;
            }
            unset($collection);

            if (count($this->_customers) == 0) {
                Mage::throwException(Mage::helper('loadtest')->__('Customers not found, please create customer(s) first'));
            }
        }
        if (is_null($this->_products)) {
            $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('name')
                ->load();
            $this->_products = array();
            foreach ($collection as $product) {
                $this->_products[$product->getId()] = $product;
            }
            unset($collection);

            if (count($this->_products) == 0) {
                Mage::throwException(Mage::helper('loadtest')->__('Products not found, please create product(s) first'));
            }
        }
        if (is_null($this->_stores)) {
            $this->_stores = array();
            $this->_storeIds = array();
            $collection = Mage::getModel('core/store')
                ->getCollection();
            foreach ($collection as $item) {
                $this->_stores[$item->getId()] = $item;
                $this->_storeIds[] = $item->getId();
            }
            unset($collection);
        }
        if (is_null($this->_reviewsData)) {
            $this->_reviewsData = file(BP . '/app/code/local/Mage/LoadTest/Data/Rating.txt');
        }
    }
}