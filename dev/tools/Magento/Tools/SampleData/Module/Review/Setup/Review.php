<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Review\Setup;

use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

/**
 * Class Review
 * @package Magento\Tools\SampleData\Module\Review\Setup
 */
class Review implements SetupInterface
{
    /**
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @var \Magento\Tools\SampleData\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\Tools\SampleData\Helper\Csv\ReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var \Magento\Review\Model\RatingFactory
     */
    protected $ratingFactory;

    /**
     * @var array
     */
    protected $productIds;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\Collection
     */
    protected $productCollection;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $customerAccount;

    /**
     * @var \Magento\Review\Model\Resource\Rating\Collection
     */
    protected $ratings;

    /**
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param \Magento\Review\Model\RatingFactory $ratingFactory
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory
     */
    public function __construct(
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccount
    ) {
        $this->reviewFactory = $reviewFactory;
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->ratingFactory = $ratingFactory;
        $this->productCollection = $productCollectionFactory->create()->addAttributeToSelect('sku');
        $this->customerAccount = $customerAccount;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo 'Installing product reviews' . PHP_EOL;

        $fixtureFile = 'Review/products_reviews.csv';
        $fixtureFilePath = $this->fixtureHelper->getPath($fixtureFile);
        /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
        $csvReader = $this->csvReaderFactory->create(array('fileName' => $fixtureFilePath, 'mode' => 'r'));
        $storeId = ['1'];
        $this->assignRatingsToWebsite($storeId);
        foreach ($csvReader as $row) {
            if (!$this->getProductIdBySku($row['sku'])) {
                continue;
            }
            $review = $this->prepareReview($row);
            if (!empty($row['email']) && ($this->getCustomerIdByEmail($row['email']) != null)) {
                $review->setCustomerId($this->getCustomerIdByEmail($row['email']));
            }
            $review->save();
            $this->setReviewRating($review, $row);
            echo '.';
        }
        echo PHP_EOL;
    }

    /**
     * Retrieve product ID by sku
     *
     * @param string $sku
     * @return int|null
     */
    protected function getProductIdBySku($sku)
    {
        if (empty($this->productIds)) {
            foreach ($this->productCollection as $product) {
                $this->productIds[$product->getSku()] = $product->getId();
            }
        }
        if (isset($this->productIds[$sku])) {
            return $this->productIds[$sku];
        }
        return null;
    }

    /**
     * @param array $row
     * @return \Magento\Review\Model\Review
     */
    protected function prepareReview($row)
    {
        /** @var $review \Magento\Review\Model\Review */
        $review = $this->reviewFactory->create();
        $review->setEntityId(
            $review->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE)
        )->setEntityPkValue(
            $this->getProductIdBySku($row['sku'])
        )->setNickname(
            $row['reviewer']
        )->setTitle(
            $row['title']
        )->setDetail(
            $row['review']
        )->setStatusId(
            \Magento\Review\Model\Review::STATUS_APPROVED
        )->setStoreId(
            1
        )->setStores(
            array(1)
        );
        return $review;
    }

    protected function getRatings()
    {
        if (!isset($this->ratings)) {
            $ratingModel = $this->ratingFactory->create();
            $this->ratings = $ratingModel->getResourceCollection();
        }
        return $this->ratings;
    }
    /**
     * @param \Magento\Review\Model\Review $review
     * @param array $row
     * @return void
     */
    protected function setReviewRating(\Magento\Review\Model\Review $review, $row)
    {
        $reviewRatings = unserialize($row['rating']);
        foreach ($this->getRatings() as $rating) {
            foreach ($rating->getOptions() as $option) {
                $optionId = $option->getOptionId();
                if (($option->getValue() == $reviewRatings[$rating->getId()]) && !empty($optionId)) {
                    $rating->setReviewId($review->getId())->addOptionVote(
                        $optionId,
                        $this->getProductIdBySku($row['sku'])
                    );
                }
            }
        }
    }

    /**
     * @param array $stores
     * @return void
     */
    protected function assignRatingsToWebsite($stores = ['1'])
    {
        $stores[] = '0';
        foreach ($this->getRatings() as $rating) {
            $rating->setStores($stores)->save();
        }
    }

    /**
     * @param string $customerEmail
     * @return int|null
     */
    protected function getCustomerIdByEmail($customerEmail)
    {
        $customerData = $this->customerAccount->getCustomerByEmail($customerEmail);
        if ($customerData) {
            return $customerData->getId();
        }
        return null;
    }
}
