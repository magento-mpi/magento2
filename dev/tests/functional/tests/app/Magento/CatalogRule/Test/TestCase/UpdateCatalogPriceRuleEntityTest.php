<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\CatalogRule\Test\Fixture\CatalogRule;
use Magento\CatalogRule\Test\Page\Adminhtml\CatalogRuleIndex;
use Magento\CatalogRule\Test\Page\Adminhtml\CatalogRuleNew;

/**
 * Test Creation for UpdateCatalogPriceRuleEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Catalog Price Rule is created
 * Steps:
 * 1. Login to backend
 * 2. Navigate to MARKETING > Catalog Price Rules
 * 3. Click Catalog Price Rule from grid
 * 4. Edit test value(s) according to dataSet
 * 5. Click 'Save' button
 * 6. Perform all asserts
 *
 * @group Catalog_Price_Rules_(MX)
 * @ZephyrId MAGETWO-20616
 */
class UpdateCatalogPriceRuleEntityTest extends Injectable
{
    /**
     * Page CatalogRuleIndex
     *
     * @var CatalogRuleIndex
     */
    protected $catalogRuleIndex;

    /**
     * Page CatalogRuleNew
     *
     * @var CatalogRuleNew
     */
    protected $catalogRuleNew;

    /**
     * Injection data
     *
     * @param CatalogRuleIndex $catalogRuleIndex
     * @param CatalogRuleNew $catalogRuleNew
     */
    public function __inject(
        CatalogRuleIndex $catalogRuleIndex,
        CatalogRuleNew $catalogRuleNew
    ) {
        $this->catalogRuleIndex = $catalogRuleIndex;
        $this->catalogRuleNew = $catalogRuleNew;
    }

    /**
     * Update Catalog Price Rule test
     *
     * @param CatalogRule $catalogPriceRule
     * @param CatalogRule $catalogPriceRuleOriginal
     */
    public function testUpdateCatalogPriceRule(
        CatalogRule $catalogPriceRule,
        CatalogRule $catalogPriceRuleOriginal
    ) {
        // Preconditions
        $catalogPriceRuleOriginal->persist();

        $filter = [
            'name' => $catalogPriceRuleOriginal->getName(),
            'rule_id' => $catalogPriceRuleOriginal->getId()
        ];

        // Steps
        $this->catalogRuleIndex->open();
        $this->catalogRuleIndex->getCatalogRuleGrid()->searchAndOpen($filter);
        $this->catalogRuleNew->getEditForm()->fill($catalogPriceRule);
        $this->catalogRuleNew->getFormPageActions()->save();
    }

}
