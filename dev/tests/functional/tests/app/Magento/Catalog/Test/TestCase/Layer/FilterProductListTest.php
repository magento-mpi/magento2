<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Layer;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Catalog\Test\Fixture\SimpleProduct;

/**
 * Class FilterProductListTest
 * Filtering product in the Frontend via layered navigation
 *
 * @package Magento\Catalog\Test\TestCase\Layer
 */
class FilterProductListTest extends Functional
{
    /**
     * Using layered navigation to filter product list
     *
     * @ZephyrId MAGETWO-12419
     */
    public function testFilterProduct()
    {
        //Preconditions
        $config = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $config->switchData('manual_layered_navigation_mysql');
        $config->persist();

        //Create anchor category
        $category = Factory::getFixtureFactory()->getMagentoCatalogCategory();
        $category->switchData('anchor_category');
        $category->persist();

        //Create simple product
        $simple = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct(array(
            SimpleProduct::PRICE_VALUE => '20',
            'categories' => ['category' => $category],
        ));
        $simple->switchData('simple');
        $simple->persist();
        $simpleName = $simple->getProductName();

        //Create configurable product
        $configurable = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct(array(
            'categories' => ['category' => $category],
        ));
        $configurable->switchData('configurable');
        $configurable->persist();
        $configurableName = $configurable->getProductName();
        $option = $configurable->getData('fields/configurable_attributes_data/value/0/0/option_label/value');

        //Steps
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $frontendHomePage->open();
        $frontendHomePage->getTopmenu()->selectCategoryByName($category->getCategoryName());
        $layeredNavigation = $categoryPage->getLayeredNavigationBlock();
        $layeredNavigation->selectPriceRange('10-20');

        //Verifying
        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertTrue($productListBlock->isProductVisible($configurableName), 'Product was not found.');
        $this->assertFalse($productListBlock->isProductVisible($simpleName), 'Product displays in filtering results.');

        //Steps
        $layeredNavigation->clearAll();
        $layeredNavigation->selectAttributeOption($option);

        //Verifying
        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertTrue($productListBlock->isProductVisible($configurableName), 'Product was not found.');
        $this->assertFalse($productListBlock->isProductVisible($simpleName), 'Product displays in filtering results.');
    }
}
