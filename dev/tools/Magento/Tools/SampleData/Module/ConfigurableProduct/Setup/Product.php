<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\ConfigurableProduct\Setup;

use Magento\Framework\File\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

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
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $configurableProductType;

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
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProductType
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param Product\Converter $converter
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProductType,
        \Magento\Catalog\Model\Config $catalogConfig,
        Product\Converter $converter,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory
    ) {
        $this->productFactory = $productFactory;
        $this->configurableProductType = $configurableProductType;
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
        echo "Installing configurable products\n";

        $product = $this->productFactory->create();

        $files = [
            'ConfigurableProduct/products_men_tops.csv',
            'ConfigurableProduct/products_men_bottoms.csv',
            'ConfigurableProduct/products_women_tops.csv',
            'ConfigurableProduct/products_women_bottoms.csv'
        ];

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
                    ->setTypeId(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE)
                    ->setAttributeSetId($attributeSetId)
                    ->setWebsiteIds(array(1))
                    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
                    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                    ->setStockData(array('is_in_stock' => 1, 'manage_stock' => 0));

                $simpleIds = $this->configurableProductType->generateSimpleProducts($product, $data['variations_matrix']);
                $product->setAssociatedProductIds($simpleIds);
                $product->setCanSaveConfigurableAttributes(true);

                $product->save();
                echo '.';
            }
        }
        echo "\n";
    }
}
