<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SampleData\Test\TestCase;

use Magento\Catalog\Test\Fixture\CatalogCategory;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;

/**
 * Class PredefineBuildConfig
 * Predefine Build config for the regression testing
 *
 * @ticketId MTA-404
 */
class PredefineBuildConfig extends Injectable
{
    /**
     * Sub category for product
     *
     * @var array
     */
    protected $subCategory = [
        'with_custom_option' => 'With Custom Options',
        'bundle_dynamic_product' => 'Dynamic',
        'bundle_fixed_product' => 'Fixed',
    ];

    /**
     * Predefine Build config for the regression testing
     *
     * @param FixtureFactory $fixtureFactory
     * @param string $products
     * @param CatalogCategory $category
     * @return void
     */
    public function test(FixtureFactory $fixtureFactory, $products, CatalogCategory $category)
    {
        $productsData = explode(', ', $products);
        foreach ($productsData as $value) {
            $product = explode('::', $value);
            if (isset($this->subCategory[$product[1]])) {
                if (!$category->hasData('id')) {
                    $category->persist();
                }
                $category = $fixtureFactory->createByCode(
                    'catalogCategory',
                    [
                        'dataSet' => 'default_anchor_subcategory',
                        'data' =>
                            [
                                'name' => $this->subCategory[$product[1]],
                                'parent_id' => $category->getId(),
                            ]
                    ]
                );
            }
            $product = $fixtureFactory->createByCode(
                $product[0],
                [
                    'dataSet' => $product[1],
                    'data' => [
                        'category_ids' => [
                            'category' => $category
                        ]
                    ]
                ]
            );
            $product->persist();
        }
    }
}
