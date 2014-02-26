<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Fixture\Category;
use Magento\Bundle\Test\Fixture\CatalogProductBundle;
use Magento\Catalog\Test\Page\Product\CatalogProductNew;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;

/**
 * Class CreateBundleEntityTest
 *
 * @package Magento\Bundle\Test\TestCase
 */
class CreateBundleEntityTest extends Injectable
{
    /**
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
        $productBlockForm = $createProductPage->getProductBlockForm();
        $productBlockForm->setCategory($this->category);
        $productBlockForm->fill($bundle);
        // Step 3
        $productBlockForm->save($bundle);
    }
}
