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
     * @var \Magento\Tools\SampleData\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $customerAccount;

    /**
     * @var \Magento\Review\Model\Rating\OptionFactory
     */
    protected $ratingOptionsFactory;

    /**
     * @var array
     */
    protected $ratings;

    /**
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param \Magento\Review\Model\RatingFactory $ratingFactory
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccount
     * @param \Magento\Tools\SampleData\Logger $logger
     * @param \Magento\Review\Model\Rating\OptionFactory $ratingOptionsFactory
     */
    public function __construct(
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccount,
        \Magento\Tools\SampleData\Logger $logger,
        \Magento\Review\Model\Rating\OptionFactory $ratingOptionsFactory
    ) {
        $this->reviewFactory = $reviewFactory;
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->ratingFactory = $ratingFactory;
        $this->productCollection = $productCollectionFactory->create()->addAttributeToSelect('sku');
        $this->logger = $logger;
        $this->customerAccount = $customerAccount;
        $this->ratingOptionsFactory = $ratingOptionsFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing product reviews' . PHP_EOL);
        $fixtureFile = 'Review/products_reviews.csv';
        $fixtureFilePath = $this->fixtureHelper->getPath($fixtureFile);
        /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
        $csvReader = $this->csvReaderFactory->create(array('fileName' => $fixtureFilePath, 'mode' => 'r'));
        foreach ($csvReader as $row) {
            $storeId = ['1'];
            $this->createRating($row['rating_code'], $storeId);
            if (!$this->getProductIdBySku($row['sku'])) {
                continue;
            }
            $review = $this->prepareReview($row);
            if (!empty($row['email']) && ($this->getCustomerIdByEmail($row['email']) != null)) {
                $review->setCustomerId($this->getCustomerIdByEmail($row['email']));
            }
            $review->save();
            $this->setReviewRating($review, $row);
            $this->logger->log('.');
        }
        $this->logger->log(PHP_EOL);
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

    /**
     * @param string $rating
     * @return array
     */
    protected function getRating($rating)
    {
        $ratingCollection = $this->ratingFactory->create()->getResourceCollection();
        if (!$ratingCollection) {
            $this->ratings = [];
        }
        if (!$this->ratings[$rating]) {
            $this->ratings[$rating] = $ratingCollection->addFieldToFilter('rating_code', $rating)->getFirstItem();
        }
        return $this->ratings[$rating];
    }
    /**
     * @param \Magento\Review\Model\Review $review
     * @param array $row
     * @return void
     */
    protected function setReviewRating(\Magento\Review\Model\Review $review, $row)
    {
        $rating = $this->getRating($row['rating_code']);
        foreach ($rating->getOptions() as $option) {
            $optionId = $option->getOptionId();
            if (($option->getValue() == $row['rating_value']) && !empty($optionId)) {
                $rating->setReviewId($review->getId())->addOptionVote(
                    $optionId,
                    $this->getProductIdBySku($row['sku'])
                );
            }
        }
    }

    /**
     * @param string $ratingCode
     * @param array $stores
     * @return void
     */
    protected function createRating($ratingCode, $stores = ['1'])
    {
        $stores[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        $rating = $this->getRating($ratingCode);
        if (!$rating->getData()) {
            $ratingModel = $this->ratingFactory->create();
            $ratingModel->setRatingCode(
                $ratingCode
            )->setStores(
                $stores
            )->setIsActive(
                '1'
            )->setEntityId(
                '1'
            )->save();

            /**Create rating options*/
            $options = [
                1 => '1',
                2 => '2',
                3 => '3',
                4 => '4',
                5 => '5'
            ];
            foreach ($options as $key => $optionCode) {
                $optionModel = $this->ratingOptionsFactory->create();
                $optionModel->setCode(
                    $optionCode
                )->setValue(
                    $key
                )->setRatingId(
                    $ratingModel->getId()
                )->setPosition(
                    $key
                )->save();
            }
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
