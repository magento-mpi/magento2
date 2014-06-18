<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\TestCase;

use Magento\CatalogRule\Test\Fixture\CatalogRule;

/**
 * Test Coverage for Create Catalog Rule
 *
 * @ticketId MAGETWO-
 */
class CreateCatalogRuleTest extends CatalogRuleEntityTest
{
    /**
     * Create Catalog Price Rule
     *
     * @param CatalogRule $catalogPriceRule
     * @return void
     */
    public function testCreate(CatalogRule $catalogPriceRule)
    {
        // Open Catalog Price Rule page
        $this->catalogRuleIndex->open();

        // Add new Catalog Price Rule
        $this->catalogRuleIndex->getGridPageActions()->addNew();

        // Fill and Save the Form
        $this->catalogRuleNew->getEditForm()->fill($catalogPriceRule);
        $this->catalogRuleNew->getFormPageActions()->save();

        // Apply Catalog Price Rule
        $this->catalogRuleIndex->getGridPageActions()->applyRules();

        // Flush cache
        $this->adminCache->open();
        $this->adminCache->getActionsBlock()->flushMagentoCache();
        $this->adminCache->getMessagesBlock()->assertSuccessMessage();

        // Prepare data for tear down
        $this->catalogRules = $catalogPriceRule;
    }
}
