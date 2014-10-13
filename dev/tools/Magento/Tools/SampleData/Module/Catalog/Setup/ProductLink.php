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
 * Product links setup
 */
class ProductLink implements SetupInterface
{
    /**
     * @var CsvReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var FixtureHelper
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks
     */
    protected $linksInitializer;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /**
     * @param CsvReaderFactory $csvReaderFactory
     * @param FixtureHelper $fixtureHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $linksInitializer
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     */
    public function __construct(
        CsvReaderFactory $csvReaderFactory,
        FixtureHelper $fixtureHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $linksInitializer,
        \Magento\Framework\Module\ModuleListInterface $moduleList
    ) {
        $this->csvReaderFactory = $csvReaderFactory;
        $this->fixtureHelper = $fixtureHelper;
        $this->productFactory = $productFactory;
        $this->linksInitializer = $linksInitializer;
        $this->moduleList = $moduleList;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo "Installing product links\n";
        $entityFileAssociation = [
            'related',
            'upsell',
            'crosssell'
        ];

        foreach (array_keys($this->moduleList->getModules()) as $moduleName) {
            foreach ($entityFileAssociation as $linkType) {
                $fileName = substr($moduleName, strpos($moduleName, "_") + 1) . '/Links/' . $linkType . '.csv';
                $fileName = $this->fixtureHelper->getPath($fileName);
                if (!$fileName) {
                    continue;
                }
                /** @var \Magento\Tools\SampleData\Helper\Csv\ReaderFactory $csvReader */
                $csvReader = $this->csvReaderFactory->create(array('fileName' => $fileName, 'mode' => 'r'));
                foreach ($csvReader as $row) {
                    /** @var \Magento\Catalog\Model\Product $product */
                    $product = $this->productFactory->create();
                    $productId1 = $product->getIdBySku($row['sku1']);
                    if (!$productId1) {
                        continue;
                    }
                    $product->setId($productId1);
                    $links = [$linkType => []];
                    foreach (explode("\n", $row['sku2']) as $productSku2) {
                        $productId2 = $product->getIdBySku($productSku2);
                        if ($productId2) {
                            $links[$linkType][$productId2] = [];
                        }
                    }
                    $product = $this->linksInitializer->initializeLinks($product, $links);
                    $product->getLinkInstance()->saveProductRelations($product);
                    echo '.';
                }
            }
        }
    }
}
