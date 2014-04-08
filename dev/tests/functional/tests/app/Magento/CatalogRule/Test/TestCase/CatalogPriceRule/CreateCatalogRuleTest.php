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
     * @var \Magento\Backend\Test\Page\AdminCache
     */
    protected $adminCache;
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
     * @param \Magento\Backend\Test\Page\AdminCache $adminCache
     */
    public function __inject(
        Category $category,
        Page\CatalogRule $catalogRuleInGrid,
        Page\CatalogRuleNew $catalogRuleNew,
        \Magento\Backend\Test\Page\AdminCache $adminCache
    ) {
        $this->category = $category;
        $this->catalogRuleInGrid = $catalogRuleInGrid;
        $this->catalogRuleNew = $catalogRuleNew;
        $this->adminCache = $adminCache;
    }

    /**
     * @param Fixture\CatalogRule $catalogRule
     */
    public function testCreate(
        Fixture\CatalogRule $catalogRule
    ) {
        // Open Catalog Price Rule page
        $this->catalogRuleInGrid->open();

        // Add new Catalog Price Rule
        $catalogRuleGrid =  $this->catalogRuleInGrid->getCatalogPriceRuleGridBlock();
        $catalogRuleGrid->addNewCatalogRule();

        // Fill and Save the Form
        $newCatalogRuleForm = $this->catalogRuleNew->getCatalogPriceRuleForm();
//        $categoryId = $category->getCategoryId();
//        $catalogRule = $categoryId;
        $newCatalogRuleForm->fill($catalogRule);
        $newCatalogRuleForm->save();

        $gridBlock = $this->catalogRuleInGrid->getCatalogPriceRuleGridBlock();

        // Apply Catalog Price Rule
        $gridBlock->applyRules();

        //Flush cache
        $this->adminCache->open();
        $this->adminCache->getActionsBlock()->flushMagentoCache();
        $this->adminCache->getMessagesBlock()->assertSuccessMessage();

    }
}
