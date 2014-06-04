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
use Magento\Backend\Test\Page\AdminCache;
use Magento\CatalogRule\Test\Fixture\CatalogRule;

/**
 * Test Coverage for Create Catalog Rule
 *
 * @ticketId MAGETWO-
 */
class CreateCatalogRuleTest extends Injectable
{
    /**
     * Page CatalogRuleNew
     *
     * @var CatalogRuleNew
     */
    protected $catalogRuleNew;

    /**
     * Page CatalogRuleIndex
     *
     * @var CatalogRuleIndex
     */
    protected $catalogRuleGrid;

    /**
     * Page AdminCache
     *
     * @var AdminCache
     */
    protected $adminCache;

    /**
     * Injection data
     *
     * @param CatalogRuleIndex $catalogRuleGrid
     * @param CatalogRuleNew $catalogRuleNew
     * @param AdminCache $adminCache
     */
    public function __inject(
        CatalogRuleIndex $catalogRuleGrid,
        CatalogRuleNew $catalogRuleNew,
        AdminCache $adminCache
    ) {
        $this->catalogRuleGrid = $catalogRuleGrid;
        $this->catalogRuleNew = $catalogRuleNew;
        $this->adminCache = $adminCache;
    }

    /**
     * Create Catalog Price Rule
     *
     * @param CatalogRule $catalogRule
     */
    public function testCreate(CatalogRule $catalogRule)
    {
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
