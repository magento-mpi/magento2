<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Fixture\Category;
use Magento\Bundle\Test\Fixture\CatalogProductBundle;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductNew;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;

/**
 * Class CreateBundleEntityTest
 * Create bundle product entity test
 */
class CreateBundleEntityTest extends Injectable
{
    /**
     * Category fixture
     *
     * @var Category
     */
    protected $category;

    /**
     * @param Category $category
     * @return array
     */
    public function __prepare(Category $category)
    {
        $category->persist();
        return [
            'category' => $category
        ];
    }

    /**
     * @param Category $category
     */
    public function __inject(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Creating bundle product and assigning it to the category
     *
     * @ZephyrId MAGETWO-12702, MAGETWO-12622
     * @param CatalogProductBundle $bundle
     * @param CatalogProductIndex $manageProductsGrid
     * @param CatalogProductNew $createProductPage
     * @return void
     */
    public function testCreate(
        CatalogProductBundle $bundle,
        CatalogProductIndex $manageProductsGrid,
        CatalogProductNew $createProductPage
    ) {
        // Step 1
        $manageProductsGrid->open();
        $manageProductsGrid->getProductBlock()->addProduct('bundle');
        // Step 2
        $productForm = $createProductPage->getForm();
        $productForm->setCategory($this->category);
        $productForm->fill($bundle);
        // Step 3
        $createProductPage->getFormAction()->save();
    }
}
