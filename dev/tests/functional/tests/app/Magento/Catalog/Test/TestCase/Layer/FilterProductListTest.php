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
 */
class FilterProductListTest extends Functional
{
    /**
     * Using layered navigation to filter product list
     *
     * @ZephyrId MAGETWO-12419
     * @return void
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
        $simple = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct([
            SimpleProduct::PRICE_VALUE => '20',
            'categories' => ['category' => $category],
        ]);
        $simple->switchData('simple');
        $simple->persist();
        $simpleName = $simple->getName();

        //Create configurable product
        $configurable = Factory::getFixtureFactory()->getMagentoConfigurableProductConfigurableProduct([
            'categories' => ['category' => $category],
        ]);
        $configurable->switchData('configurable');
        $configurable->persist();
        $configurableName = $configurable->getName();
        $option = $configurable->getData('fields/configurable_attributes_data/value/0/0/option_label/value');

        //Steps
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $frontendHomePage->open();
        $frontendHomePage->getTopmenu()->selectCategoryByName($category->getCategoryName());

        //Verifying
        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertTrue($productListBlock->isProductVisible($simpleName), 'Product was not found.');
        $this->assertTrue($productListBlock->isProductVisible($configurableName), 'Product was not found.');

        //Steps
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
