<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Downloadable\Setup;

use Magento\Framework\File\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

/**
 * Class Product
 */
class Product extends \Magento\Tools\SampleData\Module\Catalog\Setup
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

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
     * @var \Magento\Framework\File\Csv\ReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param Product\Converter $converter
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        Product\Converter $converter,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory
    ) {
        $this->productFactory = $productFactory;
        $this->catalogConfig = $catalogConfig;
        $this->converter = $converter;
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo "Installing downloadable products\n";

        $product = $this->productFactory->create();

        $files = [
            'Downloadable/products_training_video_download.csv',
        ];
        $downloadableFiles = [
            'Downloadable/downloadable_data_training_video_download.csv',
        ];
        foreach ($downloadableFiles as $downloadableFile) {
            $downloadableFileName = $this->fixtureHelper->getPath($downloadableFile);
            $csvDownloadableReader = $this->csvReaderFactory
                ->create(array('fileName' => $downloadableFileName, 'mode' => 'r'));
            foreach ($csvDownloadableReader as $downloadableRow) {
                $sku = $downloadableRow['product_sku'];
                if (!isset($downloadableData[$sku])) {
                    $downloadableData[$sku] = array();
                }
                $downloadableData[$sku] = $this->converter->getDownloadableData(
                    $downloadableRow,
                    $downloadableData[$sku]
                );
            }
        }

        foreach ($files as $file) {
            /** @var \Magento\Framework\File\Csv\Reader $csvReader */
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
                    ->setTypeId(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE)
                    ->setAttributeSetId($attributeSetId)
                    ->setWebsiteIds(array(1))
                    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
                    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                    ->setStockData(array('is_in_stock' => 1, 'manage_stock' => 0))
                    ->setStoreId(0);

                if (isset($downloadableData[$data['sku']])) {
                    $product->setDownloadableData($downloadableData[$data['sku']]);
                }
                $product->save();
                echo '.';
            }
        }
        echo "\n";
    }
}
