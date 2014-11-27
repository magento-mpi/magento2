<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Catalog\Setup;

use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

/**
 * Class Product
 * @package Magento\Tools\SampleData\Module\Catalog\Setup
 */
class Product implements SetupInterface
{
    /**
     * @var string
     */
    protected $productType = \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var int
     */
    protected $attributeSetId;

    /**
     * @var \Magento\Catalog\Model\Config
     */
    protected $catalogConfig;

    /**
     * @var Product\Converter
     */
    protected $converter;

    /**
     * @var \Magento\Tools\SampleData\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\Tools\SampleData\Helper\Csv\ReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var \Magento\Tools\SampleData\Helper\Csv\ReaderFactory
     */
    protected $fixtures;

    /**
     * @var Product\Gallery
     */
    protected $gallery;

    /**
     * @var \Magento\Tools\SampleData\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param Product\Converter $converter
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param Product\Gallery $gallery
     * @param \Magento\Tools\SampleData\Logger $logger
     * @param array $fixtures
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        Product\Converter $converter,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        Product\Gallery $gallery,
        \Magento\Tools\SampleData\Logger $logger,
        $fixtures = array(
            'Catalog/SimpleProduct/products_gear_bags.csv',
            'Catalog/SimpleProduct/products_gear_fitness_equipment.csv',
            'Catalog/SimpleProduct/products_gear_fitness_equipment_ball.csv',
            'Catalog/SimpleProduct/products_gear_fitness_equipment_strap.csv',
            'Catalog/SimpleProduct/products_gear_watches.csv',
        )
    ) {
        $this->productFactory = $productFactory;
        $this->catalogConfig = $catalogConfig;
        $this->converter = $converter;
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->gallery = $gallery;
        $this->fixtures = $fixtures;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log("Installing {$this->productType} products" . PHP_EOL);

        $product = $this->productFactory->create();

        foreach ($this->fixtures as $file) {
            /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
            $fileName = $this->fixtureHelper->getPath($file);
            $csvReader = $this->csvReaderFactory->create(array('fileName' => $fileName, 'mode' => 'r'));
            foreach ($csvReader as $row) {

                $attributeSetId = $this->catalogConfig->getAttributeSetId(4, $row['attribute_set']);

                $this->converter->setAttributeSetId($attributeSetId);
                $data = $this->converter->convertRow($row);

                /** @var $product \Magento\Catalog\Model\Product */
                $product->unsetData();
                $product->setData($data);
                $product
                    ->setTypeId($this->productType)
                    ->setAttributeSetId($attributeSetId)
                    ->setWebsiteIds(array(1))
                    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                    ->setStockData(array('is_in_stock' => 1, 'manage_stock' => 0))
                    ->setStoreId(0);

                if (empty($data['visibility'])) {
                    $product->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
                }

                $this->prepareProduct($product, $data);

                $product->save();
                $this->gallery->install($product);
                $this->logger->log('.');
            }
        }
        $this->logger->log(PHP_EOL);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $product
     * @param array $data
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function prepareProduct($product, $data)
    {
        return $this;
    }

    /**
     * Set fixtures
     *
     * @param array $fixtures
     * @return $this
     */
    public function setFixtures(array $fixtures)
    {
        $this->fixtures = $fixtures;
        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $product
     * @return void
     */
    public function setVirtualStockData($product)
    {
        $product->setStockData(
            [
                'use_config_manage_stock' => 0,
                'is_in_stock' => 1,
                'manage_stock' => 0
            ]
        );
    }
}
