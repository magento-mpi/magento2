<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\SampleData\Module\Bundle\Setup;

use Magento\Framework\File\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\Module\Catalog\Setup\Product\Gallery;
use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

/**
 * Setup bundle product
 */
class Product implements SetupInterface
{
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
     * @var \Magento\Framework\File\Csv\ReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var Gallery
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
        echo "Installing bundle products\n";

        $product = $this->productFactory->create();

        $files = [
            'Bundle/yoga_bundle.csv',
        ];
        $this->gallery->setFixtures([
            'Bundle/images_yoga_bundle.csv'
        ]);
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
                    ->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE)
                    ->setAttributeSetId($attributeSetId)
                    ->setWebsiteIds(array(1))
                    ->setStoreId(0)
                    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
                    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                    ->setStockData(array('is_in_stock' => 1, 'manage_stock' => 0));

                $product->setCanSaveConfigurableAttributes(true);
                $product->setCanSaveBundleSelections(true)
                    ->setPriceType(0);

                $product->save();
                $this->gallery->install($product);
                echo '.';
            }
        }
        echo "\n";
    }
}
