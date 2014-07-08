<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Bundle\Test\Fixture\CatalogProductBundle;
use Magento\Catalog\Test\Fixture\CatalogCategory;
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
     * @var CatalogCategory
     */
    protected $category;

    /**
     * @param CatalogCategory $category
     * @return array
     */
    public function __prepare(CatalogCategory $category)
    {
        $category->persist();
        return [
            'category' => $category
        ];
    }

    /**
     * @param CatalogCategory $category
     */
    public function __inject(CatalogCategory $category)
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
        $productForm->fillProduct($bundle, $this->category);
        // Step 3
        $createProductPage->getFormAction()->save();
    }
}
