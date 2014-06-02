<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\TestCase;

use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;
use Magento\CatalogRule\Test\Fixture\CatalogRule;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\CatalogRule\Test\Page\Adminhtml\CatalogRuleIndex;
use Magento\CatalogRule\Test\Page\Adminhtml\CatalogRuleNew;

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
     * Create simple product with category
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(
        FixtureFactory $fixtureFactory
    ) {
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
     * @param string $is_applied
     * @return void
     */
    public function testUpdateCatalogPriceRule(
        CatalogRule $catalogPriceRule,
        CatalogRule $catalogPriceRuleOriginal,
        CatalogProductSimple $product,
        $is_applied
    ) {
        // Preconditions
        $catalogPriceRuleOriginal->persist();

        //Prepare data
        $replace = [
            'conditions' => [
                'conditions' => [
                    '%category_1%' => $product->getCategoryIds()[0]['id'],
                ],
            ],
        ];
        $filter = [
            'name' => $catalogPriceRuleOriginal->getName(),
            'rule_id' => $catalogPriceRuleOriginal->getId()
        ];

        // Steps
        $this->catalogRuleIndex->open();
        $this->catalogRuleIndex->getCatalogRuleGrid()->searchAndOpen($filter);
        if ($is_applied == 'No') {
            $this->catalogRuleNew->getEditForm()->fill($catalogPriceRule);
            $this->catalogRuleNew->getFormPageActions()->save();
        } else {
            $this->catalogRuleNew->getEditForm()->fill($catalogPriceRule, null, $replace);
            $this->catalogRuleNew->getFormPageActions()->saveAndApply();
        }
    }
}
