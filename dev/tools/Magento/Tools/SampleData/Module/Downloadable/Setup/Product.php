<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Downloadable\Setup;

use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\Module\Catalog\Setup\Product\Gallery;
use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

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
    protected $downloadableData = array();

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param Product\Converter $converter
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param Gallery $gallery
     * @param array $fixtures
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        Product\Converter $converter,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        Gallery $gallery,
        $fixtures = array(
            'Downloadable/products_training_video_download.csv'
        )
    ) {
        parent::__construct(
            $productFactory,
            $catalogConfig,
            $converter,
            $fixtureHelper,
            $csvReaderFactory,
            $gallery,
            $fixtures
        );
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->gallery->setFixtures([
                'Downloadable/images_products_training_video.csv'
        ]);
        $downloadableFiles = [
            'Downloadable/downloadable_data_training_video_download.csv',
        ];
        foreach ($downloadableFiles as $downloadableFile) {
            $downloadableFileName = $this->fixtureHelper->getPath($downloadableFile);
            $csvDownloadableReader = $this->csvReaderFactory
                ->create(array('fileName' => $downloadableFileName, 'mode' => 'r'));
            foreach ($csvDownloadableReader as $downloadableRow) {
                $sku = $downloadableRow['product_sku'];
                if (!isset($this->downloadableData[$sku])) {
                    $this->downloadableData[$sku] = array();
                }
                $this->downloadableData[$sku] = $this->converter->getDownloadableData(
                    $downloadableRow,
                    $this->downloadableData[$sku]
                );
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
        return $this;
    }
}
