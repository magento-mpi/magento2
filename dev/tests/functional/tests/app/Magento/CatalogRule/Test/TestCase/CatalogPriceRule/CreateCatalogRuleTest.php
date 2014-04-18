<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\TestCase\CatalogPriceRule;

use Mtf\TestCase\Injectable;
use Magento\CatalogRule\Test\Page\Adminhtml\CatalogRuleNew;
use Magento\CatalogRule\Test\Page\Adminhtml\CatalogRuleIndex;
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
     * @var CatalogRuleNew
     */
    protected $catalogRuleNew;

    /**
     * @var CatalogRuleIndex
     */
    protected $catalogRuleGrid;

    /**
     * @var \Magento\Backend\Test\Page\AdminCache
     */
    protected $adminCache;

    /**
     * @param CatalogRuleIndex $catalogRuleGrid
     * @param CatalogRuleNew $catalogRuleNew
     * @param \Magento\Backend\Test\Page\AdminCache $adminCache
     */
    public function __inject(
        CatalogRuleIndex $catalogRuleGrid,
        CatalogRuleNew $catalogRuleNew,
        \Magento\Backend\Test\Page\AdminCache $adminCache
    ) {
        $this->catalogRuleGrid = $catalogRuleGrid;
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
        $this->catalogRuleGrid->open();

        // Add new Catalog Price Rule
        $this->catalogRuleGrid->getGridPageActions()->addNew();

        // Fill and Save the Form
        $this->catalogRuleNew->getEditForm()->fill($catalogRule);
        $this->catalogRuleNew->getFormPageActions()->save();

        // Apply Catalog Price Rule
        $this->catalogRuleGrid->getGridPageActions()->applyRules();

        // Flush cache
        $this->adminCache->open();
        $this->adminCache->getActionsBlock()->flushMagentoCache();
        $this->adminCache->getMessagesBlock()->assertSuccessMessage();
    }
}
