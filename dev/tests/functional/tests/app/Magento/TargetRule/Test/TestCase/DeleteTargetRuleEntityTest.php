<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Test\TestCase;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;
use Magento\TargetRule\Test\Fixture\TargetRule;
use Magento\TargetRule\Test\Page\Adminhtml\TargetRuleIndex;
use Magento\TargetRule\Test\Page\Adminhtml\TargetRuleEdit;

/**
 * Test Creation for DeleteTargetRuleEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Target Rule is created.
 *
 * Steps:
 * 1. Log in as default admin user.
 * 2. Go to Marketing > Related Products Rules
 * 3. Select required Target rule from preconditions
 * 4. Click on the "Delete" button
 * 5. Perform all assertions
 *
 * @group Target_Rules_(MX)
 * @ZephyrId MAGETWO-24856
 */
class DeleteTargetRuleEntityTest extends Injectable
{
    /**
     * @var TargetRuleIndex
     */
    protected $targetRuleIndex;

    /**
     * @var TargetRuleEdit
     */
    protected $targetRuleEdit;

    /**
     * Prepare data
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $product1 = $fixtureFactory->createByCode(
            'catalogProductSimple',
            ['dataSet' => 'product_with_category']
        );
        $product2 = $fixtureFactory->createByCode(
            'catalogProductSimple',
            ['dataSet' => 'product_with_special_price_and_category']
        );

        $product1->persist();
        $product2->persist();
        return [
            'product1' => $product1,
            'product2' => $product2,
        ];
    }

    /**
     * Injection data
     *
     * @param TargetRuleIndex $targetRuleIndex
     * @param TargetRuleEdit $targetRuleEdit
     * @return void
     */
    public function __inject(TargetRuleIndex $targetRuleIndex, TargetRuleEdit $targetRuleEdit)
    {
        $this->targetRuleIndex = $targetRuleIndex;
        $this->targetRuleEdit = $targetRuleEdit;
    }

    /**
     * Run delete TargetRule entity test
     *
     * @param FixtureFactory $fixtureFactory
     * @param TargetRule $targetRule
     * @param CatalogProductSimple $product1
     * @param CatalogProductSimple $product2
     * @return void
     */
    public function testDeleteTargetRuleEntity(
        FixtureFactory $fixtureFactory,
        TargetRule $targetRule,
        CatalogProductSimple $product1,
        CatalogProductSimple $product2
    ) {
        // Preconditions
        $replace = $this->getReplaceData($product1, $product2);
        $data = $this->prepareData($targetRule->getData(), $replace);
        /** @var TargetRule $originalTargetRule */
        $originalTargetRule = $fixtureFactory->createByCode('targetRule', $data);
        $originalTargetRule->persist();

        // Steps
        $filter = ['id' => $originalTargetRule->getRuleId()];
        $this->targetRuleIndex->open();
        $this->targetRuleIndex->getTargetRuleGrid()->searchAndOpen($filter);
        $this->targetRuleEdit->getPageActions()->delete();
    }

    /**
     * Get data for replace in variations
     *
     * @param CatalogProductSimple $product1
     * @param CatalogProductSimple $product2
     * @return array
     */
    protected function getReplaceData(CatalogProductSimple $product1, CatalogProductSimple $product2)
    {
        return [
            'conditions_serialized' => [
                '%category_1%' => $product1->getDataFieldConfig('category_ids')['source']->getIds()[0],
            ],
            'actions_serialized' => [
                '%category_2%' => $product2->getDataFieldConfig('category_ids')['source']->getIds()[0],
            ],
        ];
    }

    /**
     * Replace placeholders in each values of data
     *
     * @param array $data
     * @param array $replace
     * @return array
     */
    protected function prepareData(array $data, array $replace)
    {
        foreach ($replace as $key => $pair) {
            if (isset($data[$key])) {
                $data[$key] = str_replace(
                    array_keys($pair),
                    array_values($pair),
                    $data[$key]
                );
            }
        }
        return $data;
    }
}
