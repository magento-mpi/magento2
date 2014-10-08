<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product;

use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductNew;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Fixture\CatalogProductSimple\RelatedProducts;

/**
 * Class AbstractAddRelatedProductsEntityTest
 * Base class for add related products entity test
 */
class AbstractAddRelatedProductsEntityTest extends Injectable
{
    /**
     * Fixture factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Catalog product index page on backend
     *
     * @var CatalogProductIndex
     */
    protected $catalogProductIndex;

    /**
     * Catalog product view page on backend
     *
     * @var CatalogProductNew
     */
    protected $catalogProductNew;

    /**
     * Type of related products
     *
     * @var string
     */
    protected $typeRelatedProducts = '';

    /**
     * Prepare data
     *
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Inject data
     *
     * @param CatalogProductIndex $catalogProductIndex
     * @param CatalogProductNew $catalogProductNew
     * @return void
     */
    public function __inject(CatalogProductIndex $catalogProductIndex, CatalogProductNew $catalogProductNew)
    {
        $this->catalogProductIndex = $catalogProductIndex;
        $this->catalogProductNew = $catalogProductNew;
    }

    /**
     * Run test add related products entity
     *
     * @param string $productData
     * @param string $relatedProductsData
     * @return array
     */
    public function test($productData, $relatedProductsData)
    {
        $product = $this->createProduct($productData, $relatedProductsData);
        $dataConfig = $product->getDataConfig();
        $typeId = isset($dataConfig['type_id']) ? $dataConfig['type_id'] : null;

        $this->catalogProductIndex->open();
        $this->catalogProductIndex->getGridPageActionBlock()->addProduct($typeId);
        $this->catalogProductNew->getProductForm()->fill($product);
        $this->catalogProductNew->getFormPageActions()->save($product);

        /** @var RelatedProducts $relatedProducts*/
        $relatedProducts = $product->getDataFieldConfig($this->typeRelatedProducts)['source'];
        return [
            'product' => $product,
            'relatedProducts' => $relatedProducts->getProducts()
        ];
    }

    /**
     * Create product
     *
     * @param string $productData
     * @param string $relatedProductsData
     * @return FixtureInterface
     */
    protected function createProduct($productData, $relatedProductsData)
    {
        list($fixtureCode, $dataSet) = explode('::', $productData);
        return $this->fixtureFactory->createByCode(
            $fixtureCode,
            [
                'dataSet' => $dataSet,
                'data' => [
                    $this->typeRelatedProducts => [
                        'presets' => $relatedProductsData
                    ]
                ]
            ]
        );
    }
}
