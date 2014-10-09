<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\RecurringPayment\Setup;

use Magento\Framework\File\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\Module\Catalog\Setup\Product\Gallery;
use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

/**
 * Class Product
 * Launches installing of virtual products with recurring payment
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
     * @var \Magento\Tools\SampleData\Module\RecurringPayment\Setup\Product\Converter
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
     * @param \Magento\Tools\SampleData\Module\RecurringPayment\Setup\Product\Converter $converter
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Tools\SampleData\Module\RecurringPayment\Setup\Product\Converter $converter,
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
        echo "Installing virtual products\n";

        $product = $this->productFactory->create();

        $files = [
            'RecurringPayment/products_training_subscription.csv'
        ];
        $this->gallery->setFixtures([
            'RecurringPayment/images_training_subscription.csv'
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
                    ->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL)
                    ->setAttributeSetId($attributeSetId)
                    ->setWebsiteIds(array(1))
                    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
                    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                    ->setStockData(array('is_in_stock' => 1, 'manage_stock' => 0));

                $product->save();
                $this->gallery->install($product);
                echo '.';
            }
        }
        echo "\n";
    }
}
