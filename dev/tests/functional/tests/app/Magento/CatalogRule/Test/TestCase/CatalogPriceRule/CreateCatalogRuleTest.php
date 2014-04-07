<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\TestCase\CatalogPriceRule;

use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Fixture\Category;
use Magento\CatalogRule\Test\Page;
use Magento\CatalogRule\Test\Fixture;

/**
 * Test Coverage for Create Catalog Rule
 *
 *
 * @ticketId MAGETWO-
 */
class CreateCatalogRuleTest extends Injectable
{
    /**
     * @var Category
     */
    protected $category;

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
     * @param Page\CatalogRule $catalogRuleInGrid
     * @param Page\CatalogRuleNew $catalogRuleNew
     * @param Fixture\CatalogRule $catalogRule
     */
    public function __inject(
        Category $category,
        Page\CatalogRule $catalogRuleInGrid,
        Page\CatalogRuleNew $catalogRuleNew,
        Fixture\CatalogRule $catalogRule
    ) {
        $this->category = $category;
        $this->catalogRuleinGrid = $catalogRuleInGrid;
        $this->catalogRuleNew = $catalogRuleNew;
        $this->catalogRule = $catalogRule;
    }

    /**
     * @param Category $category
     * @param Page\CatalogRule $catalogRuleInGrid
     * @param Page\CatalogRuleNew $catalogRuleNew
     * @param $catalogRule
     */
    public function testCreate(
        Category $category,
        Page\CatalogRule $catalogRuleInGrid,
        Page\CatalogRuleNew  $catalogRuleNew,
        Fixture\CatalogRule $catalogRule
    ) {
        // Open Catalog Price Rule page
        $catalogRuleInGrid->open();

        // Add new Catalog Price Rule
        $catalogRuleGrid = $catalogRuleInGrid->getCatalogPriceRuleGridBlock();
        $catalogRuleGrid->addNewCatalogRule();

        // Fill and Save the Form
        $newCatalogRuleForm = $catalogRuleNew->getCatalogPriceRuleForm();
//        $categoryId = $category->getCategoryId();
//        $catalogRule = $categoryId;
        $newCatalogRuleForm->fill($catalogRule);
        $newCatalogRuleForm->save();

        $gridBlock = $catalogRuleInGrid->getCatalogPriceRuleGridBlock();

        // Apply Catalog Price Rule
        $gridBlock->applyRules();

        //Flush cache
        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();
        $cachePage->getMessagesBlock()->assertSuccessMessage();

    }
}
