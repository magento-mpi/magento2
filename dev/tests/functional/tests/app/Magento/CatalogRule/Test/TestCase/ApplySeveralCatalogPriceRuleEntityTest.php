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

/**
 * Test Creation for Apply several CatalogPriceRuleEntity 
 *
 * Test Flow:
 * Preconditions:
 *  1. Create simple product.
 *  2. Execute before each variation:
 *   - Delete all active catalog price rules
 *   - Create catalog price rule from dataSet using Curl
 * Steps:
 *  1. Apply all created rules
 *  2. Perform all assertions
 *
 * @group Catalog_Price_Rules_(MX)
 * @ZephyrId MAGETWO-24780
 */
class ApplySeveralCatalogPriceRuleEntityTest extends CatalogRuleEntityTest
{
    /**
     * Create simple product
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(
        FixtureFactory $fixtureFactory
    ) {
        $productSimple = $fixtureFactory->createByCode('catalogProductSimple', ['dataSet' => 'simple_for_salesrule_1']);
        $productSimple->persist();

        return ['product' => $productSimple];
    }

    /**
     * Apply several catalog price rules
     *
     * @param FixtureFactory $fixtureFactory,
     * @param CatalogProductSimple $product
     * @param array $catalogRulesOriginal
     * @param array $price
     * @return void
     */
    public function testApplySeveralCatalogPriceRules(
        FixtureFactory $fixtureFactory,
        CatalogProductSimple $product,
        array $catalogRulesOriginal,
        array $price
    ) {
        // Preconditions
        foreach ($catalogRulesOriginal as $key => $catalogPriceRule) {
            if ($catalogPriceRule != '-') {
                $this->catalogRules[$key] = $fixtureFactory->createByCode(
                    'catalogRule',
                    ['dataSet' => $catalogPriceRule]
                );
                $this->catalogRules[$key]->persist();

                $filter = [
                    'name' => $this->catalogRules[$key]->getName(),
                    'rule_id' => $this->catalogRules[$key]->getId()
                ];
                $this->catalogRuleIndex->open();
                $this->catalogRuleIndex->getCatalogRuleGrid()->searchAndOpen($filter);
                $this->catalogRuleNew->getFormPageActions()->saveAndApply();
            }
        }
    }
}
