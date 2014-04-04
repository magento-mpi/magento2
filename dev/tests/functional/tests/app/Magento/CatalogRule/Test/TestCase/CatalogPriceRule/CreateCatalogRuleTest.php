<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product;

use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Fixture\Category;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductNew;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\CatalogRule\Test\Page;
use Magento\CatalogRule\Test\Fixture;

/**
 * Test Coverage for CreateProductEntity
 *
 * General Flow:
 * 1. Log in to Backend.
 * 2. Navigate to Products > Catalog.
 * 3. Start to create new product.
 * 4. Fill in data according to data set.
 * 5. Save product.
 * 6. Verify created product.
 *
 * @ticketId MAGETWO-20024
 */
class CreateCatalogRuleTest extends Injectable
{
    /**
     * @var Category
     */
    protected $category;

    /**
     * @var CatalogProductIndex
     */
    protected $productPageGrid;

    /**
     * @var CatalogProductNew
     */
    protected $newProductPage;

    /**
     * @var Page\CatalogRuleNew
     */
    protected $catalogRuleNew;

    /**
     * @var Page\CatalogRule
     */
    protected $catalogRuleInGrid;

    /**
     * @var Fixture\CatalogRule
     */
    protected $catalogRule;
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
     * @param CatalogProductIndex $productPageGrid
     * @param CatalogProductNew $newProductPage
     * @param Page\CatalogRule $catalogRuleInGrid
     * @param Page\CatalogRuleNew $catalogRuleNew
     * @param Fixture\CatalogRule $catalogRule
     */
    public function __inject(
        Category $category,
        CatalogProductIndex $productPageGrid,
        CatalogProductNew $newProductPage,
        Page\CatalogRule $catalogRuleInGrid,
        Page\CatalogRuleNew $catalogRuleNew,
        Fixture\CatalogRule $catalogRule
    ) {
        $this->category = $category;
        $this->productPageGrid = $productPageGrid;
        $this->newProductPage = $newProductPage;
        $this->catalogRuleinGrid = $catalogRuleInGrid;
        $this->catalogRuleNew = $catalogRuleNew;
        $this->catalogRule = $catalogRule;
    }

    /**
     * @param CatalogProductSimple $product
     * @param Category $category
     * @param Page\CatalogRule $catalogRuleInGrid
     * @param $catalogRuleNew
     * @param $catalogRule
     */
    public function testCreate(
        CatalogProductSimple $product,
        Category $category,
        Page\CatalogRule $catalogRuleInGrid,
        Page\CatalogRuleNew  $catalogRuleNew,
        $catalogRule
    ) {
        // Steps
        $this->productPageGrid->open();
        $this->productPageGrid->getProductBlock()->addProduct('simple');
        $productBlockForm = $this->newProductPage->getProductBlockForm();
        $productBlockForm->setCategory($category);
        $productBlockForm->fill($product);
        $productBlockForm->save($product);

        // Create new Catalog Price Rule
        $categoryIds = $product->getCategoryIds();
        $this->createNewCatalogPriceRule($categoryIds[0], $catalogRuleInGrid, $catalogRuleNew, $catalogRule);

        //Flush cache
        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();
        $cachePage->getMessagesBlock()->assertSuccessMessage();

    }

    /**
     * @param $categoryId
     * @param Page\CatalogRule $catalogRuleInGrid
     * @param Page\CatalogRuleNew $catalogRuleNew
     * @param Fixture\CatalogRule $catalogRule
     */
    public function createNewCatalogPriceRule(
        $categoryId,
        Page\CatalogRule $catalogRuleInGrid,
        Page\CatalogRuleNew $catalogRuleNew,
        Fixture\CatalogRule $catalogRule
    )
    {
        // Open Catalog Price Rule page
        $catalogRuleInGrid->open();

        // Add new Catalog Price Rule
        $catalogRuleGrid = $catalogRuleInGrid->getCatalogPriceRuleGridBlock();
        $catalogRuleGrid->addNewCatalogRule();

        // Fill and Save the Form
        $newCatalogRuleForm = $catalogRuleNew->getCatalogPriceRuleForm();
        $catalogRule['category_id'] = $categoryId;
        $newCatalogRuleForm->fill($catalogRule);
        $newCatalogRuleForm->save();

        $gridBlock = $catalogRuleInGrid->getCatalogPriceRuleGridBlock();

        // Apply Catalog Price Rule
        $gridBlock->applyRules();
    }
}
