<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\TestCase;

use Mtf\Fixture\FixtureFactory;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\CatalogRule\Test\Fixture\CatalogRule;

/**
 * Test Coverage for Create Catalog Rule
 *
 * @ticketId MAGETWO-
 */
class CreateCatalogRuleTest extends CatalogRuleEntityTest
{
    /**
     * Create simple product with category
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(
        FixtureFactory $fixtureFactory
    ) {
        $productSimple = $fixtureFactory->createByCode('catalogProductSimple', ['dataSet' => 'MAGETWO-23036']);
        $productSimple->persist();

        return ['product' => $productSimple];
    }

    /**
     * Create Catalog Price Rule
     *
     * @param CatalogRule $catalogPriceRule
     * @param CatalogProductSimple $product
     * @param array $price
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function testCreate(
        CatalogRule $catalogPriceRule,
        CatalogProductSimple $product,
        array $price
    ) {
        // Prepare data
        $replace = ['conditions' => ['conditions' => ['%category_1%' => $product->getCategoryIds()[0]['id']]]];

        // Open Catalog Price Rule page
        $this->catalogRuleIndex->open();

        // Add new Catalog Price Rule
        $this->catalogRuleIndex->getGridPageActions()->addNew();

        // Fill and Save the Form
        $this->catalogRuleNew->getEditForm()->fill($catalogPriceRule, null, $replace);
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
