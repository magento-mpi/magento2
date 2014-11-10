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
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory
    ) {
        $this->reviewFactory = $reviewFactory;
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->ratingFactory = $ratingFactory;
        $this->productCollection = $productCollectionFactory->create()->addAttributeToSelect('sku');
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
        foreach ($csvReader as $row) {
            if (!$this->getProductIdBySku($row['sku'])) {
                continue;
            }
            $review = $this->prepareReview($row);
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
    public function getProductIdBySku($sku)
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
    public function prepareReview($row)
    {
        $review = $this->reviewFactory->create();
        /** @var $review \Magento\Review\Model\Review */
        $review->unsetData();
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
     * @param \Magento\Review\Model\Review $review
     * @param array $row
     * @return void
     */
    public function setReviewRating(\Magento\Review\Model\Review $review, $row)
    {
        $this->ratingFactory->create(
        )->setRatingId(
            1
        )->setReviewId(
            $review->getId()
        )->addOptionVote(
            $row['rating'],
            $this->getProductIdBySku($row['sku'])
        );
    }
}
