<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Category;

use Magento\Catalog\Test\Fixture\CatalogCategoryEntity;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Adminhtml\CatalogCategoryIndex;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for UpdateCategoryEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Create category
 *
 * Steps:
 * 1. Login as admin
 * 2. Navigate Products->Categories
 * 3. Open category created in preconditions
 * 4. Update data according to data set
 * 5. Save
 * 6. Perform asserts
 *
 * @group Category_Management_(MX)
 * @ZephyrId MAGETWO-23290
 */
class UpdateCategoryEntityTest extends Injectable
{
    /**
     * Catalog category edit page
     *
     * @var CatalogCategoryIndex
     */
    protected $catalogCategoryIndex;

    /**
     * Inject page end prepare default category
     *
     * @param FixtureFactory $fixtureFactory
     * @param CatalogCategoryIndex $catalogCategoryIndex
     * @return array
     */
    public function __inject(FixtureFactory $fixtureFactory, CatalogCategoryIndex $catalogCategoryIndex)
    {
        $this->catalogCategoryIndex = $catalogCategoryIndex;
        /** @var CatalogProductSimple $product */
        $product = $fixtureFactory->createByCode('catalogProductSimple', ['dataSet' => 'product_with_category']);
        $product->persist();
        $catalogCategory = $product->getDataFieldConfig('category_ids')['source']->getCategory()[0];
        return ['catalogCategory' => $catalogCategory];
    }

    /**
     * Test for update category
     *
     * @param CatalogCategoryEntity $category
     * @param CatalogCategoryEntity $catalogCategory
     * @return void
     */
    public function testUpdateCategory(CatalogCategoryEntity $category, CatalogCategoryEntity $catalogCategory)
    {
        $this->catalogCategoryIndex->open();
        $this->catalogCategoryIndex->getTreeCategories()->selectCategory(
            $catalogCategory->getPath() . '/' . $catalogCategory->getName()
        );
        $this->catalogCategoryIndex->getEditForm()->fill($category);
        $this->catalogCategoryIndex->getFormPageActions()->save();
    }
}
