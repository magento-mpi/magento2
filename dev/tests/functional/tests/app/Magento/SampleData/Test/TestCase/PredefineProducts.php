<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SampleData\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Catalog\Test\Fixture\CatalogCategory;

/**
 * Class PredefineProducts
 * Predefine products
 *
 * @ticketId MTA-404
 */
class PredefineProducts extends Injectable
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
     * Predefine products
     *
     * @param FixtureFactory $fixtureFactory
     * @param string $products
     * @param CatalogCategory $category
     * @return void
     */
    public function test(FixtureFactory $fixtureFactory, $products, CatalogCategory $category)
    {
        $productsData = explode(', ', $products);
        $rootCategoryId = 1;
        foreach ($productsData as $value) {
            $product = explode('::', $value);
            if (isset($this->subCategory[$product[1]])) {
                if (!$category->hasData('id')) {
                    $category->persist();
                    $rootCategoryId = $category->getId();
                }
                $category = $fixtureFactory->createByCode(
                    'catalogCategory',
                    [
                        'dataSet' => 'default_anchor_subcategory',
                        'data' =>
                            [
                                'name' => $this->subCategory[$product[1]],
                                'parent_id' => $rootCategoryId,
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
