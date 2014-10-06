<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\GiftCard\Setup;

use Magento\Framework\File\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\Module\Catalog\Setup\Product\Gallery;
use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

/**
 * Class Product
 */
class Product implements SetupInterface
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
     * @var FixtureHelper
     */
    protected $fixtureHelper;

    /**
     * @var CsvReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var Product\Gallery
     */
    protected $gallery;

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
        CsvReaderFactory $csvReaderFactory,
        Gallery $gallery
    ) {
        $this->productFactory = $productFactory;
        $this->catalogConfig = $catalogConfig;
        $this->converter = $converter;
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->gallery = $gallery;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo 'Installing giftcard products' . PHP_EOL;
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productFactory->create();
        $this->gallery->setFixtures([
            'GiftCard/images_giftcard.csv'
        ]);
        $files = [
            'GiftCard/products_giftcard.csv',
        ];
        foreach ($files as $file) {
            /** @var \Magento\Framework\File\Csv\Reader $csvReader */
            $fileName = $this->fixtureHelper->getPath($file);
            $csvReader = $this->csvReaderFactory->create(array('fileName' => $fileName, 'mode' => 'r'));
            foreach($csvReader as $row) {
                if (empty($row)) {
                    continue;
                }
                $attributeSetId = $this->catalogConfig->getAttributeSetId(4, $row['attribute_set']);
                $data = $this->converter->convertRow($row);
                $product->unsetData();
                $product->setData($data);
                $product
                    ->setTypeId(\Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD)
                    ->setAttributeSetId($attributeSetId)
                    ->setWebsiteIds(array(1))
                    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
                    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                    ->setStockData(array('is_in_stock' => 1, 'manage_stock' => 1, 'qty' => 100));
                $product->save();
                $this->gallery->install($product);
                echo '.';
            }
        }
        echo "\n";
    }
}
