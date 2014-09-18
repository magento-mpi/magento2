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
 * Class PredefineExtendedProductsList
 * Predefine products list in category tree
 *
 * @ticketId MTA-404
 */
class PredefineExtendedProductsList extends Injectable
{
    /**
     * Predefine products list in category tree
     *
     * @param FixtureFactory $fixtureFactory
     * @param string $product
     * @param int $productQty
     * @param $productsInCategory
     * @param CatalogCategory $rootCategory
     * @param string $subCategoryName
     * @return void
     */
    public function test(
        FixtureFactory $fixtureFactory,
        $product,
        $productQty,
        $productsInCategory,
        CatalogCategory $rootCategory,
        $subCategoryName
    ) {
        $productData = explode('::', $product);
        $rootCategory->persist();
        for ($i = 0; $i < $productQty; $i++) {
            if (($i % $productsInCategory) == 0) {
                $to = $productsInCategory < $productQty - $i ? $i + $productsInCategory : $productQty;
                $from = $i + 1;
                $category = $fixtureFactory->createByCode(
                    'catalogCategory',
                    [
                        'dataSet' => 'default_anchor_subcategory',
                        'data' =>
                            [
                                'name' => "$subCategoryName from $from to $to",
                                'parent_id' => $rootCategory->getId(),
                            ]
                    ]
                );
                $category->persist();
            }
            $product = $fixtureFactory->createByCode(
                $productData[0],
                [
                    'dataSet' => $productData[1],
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
