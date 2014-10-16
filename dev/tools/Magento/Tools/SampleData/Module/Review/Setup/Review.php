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
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Review\Model\RatingFactory
     */
    protected $ratingFactory;

    /**
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory,
     * @param \Magento\Review\Model\RatingFactory $ratingFactory
     */
    public function __construct(
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Review\Model\RatingFactory $ratingFactory
    ) {
        $this->reviewFactory = $reviewFactory;
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->productFactory = $productFactory;
        $this->ratingFactory = $ratingFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo 'Installing product reviews' . PHP_EOL;

        $review = $this->reviewFactory->create();
        $productModel = $this->productFactory->create();
        $fixtureFile = 'Review/products_reviews.csv';
        $fixtureFilePath = $this->fixtureHelper->getPath($fixtureFile);
        /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
        $csvReader = $this->csvReaderFactory->create(array('fileName' => $fixtureFilePath, 'mode' => 'r'));
        foreach ($csvReader as $row) {
            $product = $productModel->loadByAttribute('sku', $row['sku']);
            if (!$product) {
                continue;
            }
            /** @var $review \Magento\Review\Model\Review */
            $review->unsetData();
            $review->setEntityId(
                $review->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE)
            )->setEntityPkValue(
                $product->getId()
            )->setNickname(
                $row['reviewer']
            )->setTitle(
                $row['title']
            )->setDetail(
                $row['review']
            )->setCreatedAt(
                $row['date']
            )->setStatusId(
                \Magento\Review\Model\Review::STATUS_APPROVED
            )->setStoreId(
                1
            )->setStores(
                array(1)
            )->save();

            $this->ratingFactory->create(
            )->setRatingId(
                1
            )->setReviewId(
                $review->getId()
            )->addOptionVote(
                $row['rating'],
                $product->getId()
            );
            echo '.';
        }
        echo PHP_EOL;
    }
}
