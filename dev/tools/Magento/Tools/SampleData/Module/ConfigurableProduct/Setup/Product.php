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
    protected $productFactory;

    protected $attributeSetId;

    protected $catalogConfig;

    protected $configurableProductType;

    protected $imageInstaller;

    protected $converter;

    protected $fixtureHelper;

    protected $csvReaderFactory;

    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProductType,
        \Magento\Catalog\Model\Config $catalogConfig,
        Product\ImageInstaller $imageInstaller,
        Product\Converter $converter,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory
    ) {
        $this->productFactory = $productFactory;
        $this->configurableProductType = $configurableProductType;
        $this->catalogConfig = $catalogConfig;
        $this->imageInstaller = $imageInstaller;
        $this->converter = $converter;
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
    }

    public function run()
    {
        echo "Installing configurable products\n";

        $product = $this->productFactory->create();

        $files = [
            'ConfigurableProduct/products_men_tops_new.csv',
            'ConfigurableProduct/products_men_bottoms_new.csv',
            'ConfigurableProduct/products_women_tops_new.csv',
            'ConfigurableProduct/products_women_bottoms_new.csv',
        ];

        foreach ($files as $file) {
            /** @var \Magento\Framework\File\Csv\Reader $csvReader */
            $fileName = $this->fixtureHelper->getPath($file);
            $csvReader = $this->csvReaderFactory->create(array('fileName' => $fileName, 'mode' => 'r'));
            foreach($csvReader as $row) {

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

                $this->imageInstaller->install($product);
            }
        }
        echo "\n";
    }
}
