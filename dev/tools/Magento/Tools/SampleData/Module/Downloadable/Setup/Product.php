<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\SampleData\Module\Downloadable\Setup;

use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;
use Magento\Tools\SampleData\Module\Catalog\Setup\Product\Gallery;
use Magento\Tools\SampleData\SetupInterface;

/**
 * Setup downloadable product
 */
class Product extends \Magento\Tools\SampleData\Module\Catalog\Setup\Product implements SetupInterface
{
    /**
     * @var string
     */
    protected $productType = \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE;

    /**
     * @var array
     */
    protected $downloadableData = [];

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param Product\Converter $converter
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param Gallery $gallery
     * @param \Magento\Tools\SampleData\Logger $logger
     * @param \Magento\Tools\SampleData\Helper\StoreManager $storeManager
     * @param array $fixtures
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        Product\Converter $converter,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        Gallery $gallery,
        \Magento\Tools\SampleData\Logger $logger,
        \Magento\Tools\SampleData\Helper\StoreManager $storeManager,
        $fixtures = [
            'Downloadable/products_training_video_download.csv',
        ]
    ) {
        parent::__construct(
            $productFactory,
            $catalogConfig,
            $converter,
            $fixtureHelper,
            $csvReaderFactory,
            $gallery,
            $logger,
            $storeManager,
            $fixtures
        );
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->gallery->setFixtures([
                'Downloadable/images_products_training_video.csv',
        ]);
        $downloadableFiles = [
            'Downloadable/downloadable_data_training_video_download.csv',
        ];
        foreach ($downloadableFiles as $downloadableFile) {
            $downloadableFileName = $this->fixtureHelper->getPath($downloadableFile);
            $csvDownloadableReader = $this->csvReaderFactory
                ->create(['fileName' => $downloadableFileName, 'mode' => 'r']);
            foreach ($csvDownloadableReader as $downloadableRow) {
                $sku = $downloadableRow['product_sku'];
                if (!isset($this->downloadableData[$sku])) {
                    $this->downloadableData[$sku] = [];
                }
                $this->downloadableData[$sku] = $this->converter->getDownloadableData(
                    $downloadableRow,
                    $this->downloadableData[$sku]
                );
                $this->downloadableData[$sku]['sample'] = $this->converter->getSamplesInfo();
            }
        }

        parent::run();
    }

    /**
     * @inheritdoc
     */
    protected function prepareProduct($product, $data)
    {
        if (isset($this->downloadableData[$data['sku']])) {
            $product->setDownloadableData($this->downloadableData[$data['sku']]);
        }
        $this->setVirtualStockData($product);
        return $this;
    }
}
