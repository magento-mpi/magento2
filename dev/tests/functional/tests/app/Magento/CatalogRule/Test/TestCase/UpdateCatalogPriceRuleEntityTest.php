<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\TestCase;

use Mtf\Fixture\FixtureFactory;
use Magento\CatalogRule\Test\Fixture\CatalogRule;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Fixture\CatalogProductSimple\CategoryIds;

/**
 * Test Creation for UpdateCatalogPriceRuleEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Simple product with category is created
 * 2. Catalog Price Rule is created
 * Steps:
 * 1. Login to backend
 * 2. Navigate to MARKETING > Catalog Price Rules
 * 3. Click Catalog Price Rule from grid
 * 4. Edit test value(s) according to dataSet
 * 5. Click 'Save'/ 'Apply' button
 * 6. Perform all asserts
 *
 * @group Catalog_Price_Rules_(MX)
 * @ZephyrId MAGETWO-25187
 */
class UpdateCatalogPriceRuleEntityTest extends CatalogRuleEntityTest
{
    /**
     * Create simple product with category
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $productSimple = $fixtureFactory->createByCode('catalogProductSimple', ['dataSet' => 'product_with_category']);
        $productSimple->persist();

        return ['product' => $productSimple];
    }

    /**
     * Update catalog price rule
     *
     * @param CatalogRule $catalogPriceRule
     * @param CatalogRule $catalogPriceRuleOriginal
     * @param CatalogProductSimple $product
     * @param string $saveAction
     * @param array $price
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function testUpdateCatalogPriceRule(
        CatalogRule $catalogPriceRule,
        CatalogRule $catalogPriceRuleOriginal,
        CatalogProductSimple $product,
        $saveAction,
        array $price
    ) {
        // Preconditions
        $catalogPriceRuleOriginal->persist();

        //Prepare data
        if ($saveAction == 'saveAndApply') {
            /** @var CategoryIds $sourceCategories */
            $sourceCategories = $product->getDataFieldConfig('category_ids')['source'];
            $replace = [
                'conditions' => [
                    'conditions' => [
                        '%category_1%' => $sourceCategories->getIds()[0]
                    ]
                ]
            ];
        } else {
            $replace = [];
        }
        $filter = [
            'name' => $catalogPriceRuleOriginal->getName(),
            'rule_id' => $catalogPriceRuleOriginal->getId()
        ];

        // Steps
        $this->catalogRuleIndex->open();
        $this->catalogRuleIndex->getCatalogRuleGrid()->searchAndOpen($filter);
        $this->catalogRuleNew->getEditForm()->fill($catalogPriceRule, null, $replace);
        $this->catalogRuleNew->getFormPageActions()->$saveAction();

        // Prepare data for tear down
        $this->catalogRules[] = $catalogPriceRule;
    }
}
