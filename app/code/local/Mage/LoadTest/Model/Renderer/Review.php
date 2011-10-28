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
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * LoadTest Renderer Reviews and Rating model
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @author      Magento Core Team <core@magentocommerce.com>
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
     * Review data for profiler
     *
     * @var array
     */
    protected $_review;

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

        $this->setCount(100);
    }

    /**
     * Render Reviews and Ratings
     *
     * @return Mage_LoadTest_Model_Renderer_Review
     */
    public function render()
    {
        $this->_profilerBegin();
        for ($i = 0; $i < $this->getCount(); $i++) {
            if (!$this->_checkMemorySuffice()) {
                $urlParams = array(
                    'count='.($this->getCount() - $i),
                    'detail_log='.$this->getDetailLog()
                );
                $this->_urls[] = Mage::getUrl('*/*/*/') . ' GET:"'.join(';', $urlParams).'"';
                break;
            }
            $this->_createReview();
        }
        $this->_profilerEnd();

        return $this;
    }

    /**
     * Delete all reviews and ratings
     *
     * @return Mage_LoadTest_Model_Renderer_Review
     */
    public function delete()
    {
        $this->_profilerBegin();
        $this->_loadData();

        $collection = Mage::getModel('review/review')
            ->getCollection()
            ->load();

        foreach ($collection as $review) {
            $this->_profilerOperationStart();

            if (isset($this->_customers[intval($review->getCustomerId())])) {
                $customer = $this->_customers[intval($review->getCustomerId())];
                $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
            }
            else {
                $customerName = Mage::helper('Mage_LoadTest_Helper_Data')->__('Guest');
            }
            $this->_review = array(
                'id'            => $review->getId(),
                'customer_id'   => $review->getCustomerId(),
                'customer_name' => $customerName,
                'product_id'    => $review->getEntityPkValue(),
                'product_name'  => $this->_products[$review->getEntityPkValue()]->getNmae(),
                'review_title'  => $review->getTitle()
            );
            $review->delete();

            $this->_profilerOperationStop();
        }
        $this->_profilerEnd();

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

        $this->_profilerOperationStart();

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
        $this->_review = array(
            'id'            => $reviewId,
            'customer_id'   => $customer->getId(),
            'customer_name' => $customerName,
            'product_id'    => $product->getId(),
            'product_name'  => $product->getName(),
            'review_title'  => $reviewTitle
        );

        unset($review);

        $this->_profilerOperationStop();

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
                Mage::throwException(Mage::helper('Mage_LoadTest_Helper_Data')->__('Customers not found, please create customer(s) first.'));
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
                Mage::throwException(Mage::helper('Mage_LoadTest_Helper_Data')->__('Products not found, please create product(s) first.'));
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

    protected function _profilerOperationStop()
    {
        parent::_profilerOperationStop();

        if ($this->getDebug()) {
            if (!$this->_xmlFieldSet) {
                $this->_xmlFieldSet = $this->_xmlResponse->addChild('reviews');
                $review = $this->_xmlFieldSet->addChild('review');
                $review->addAttribute('id', $this->_review['id']);
                $review->addChild('title', $this->_review['review_title']);

                $review->addChild('customer', $this->_review['customer_name'])
                    ->addAttribute('id', $this->_review['customer_id']);

                $review->addChild('product', $this->_review['product_name'])
                    ->addAttribute('id', $this->_review['product_id']);

                $this->_profilerOperationAddDebugInfo($review);
            }
        }
    }
}