<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\TargetRule\Test\Page\Adminhtml\TargetRuleEdit;
use Magento\TargetRule\Test\Page\Adminhtml\TargetRuleIndex;
use Magento\TargetRule\Test\Fixture\TargetRule;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

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
        $product = $fixtureFactory->createByCode(
            'catalogProductSimple',
            ['dataSet' => 'product_with_category']
        );
        $sellingProduct = $fixtureFactory->createByCode(
            'catalogProductSimple',
            ['dataSet' => 'product_with_special_price_and_category']
        );

        $product->persist();
        $sellingProduct->persist();
        return [
            'product' => $product,
            'sellingProduct' => $sellingProduct,
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
     * @param CatalogProductSimple $product
     * @param CatalogProductSimple $sellingProduct
     * @return array
     */
    public function testDeleteTargetRuleEntity(
        FixtureFactory $fixtureFactory,
        TargetRule $targetRule,
        CatalogProductSimple $product,
        CatalogProductSimple $sellingProduct
    ) {
        // Preconditions
        $replace = $this->getReplaceData($product, $sellingProduct);
        $data = $this->prepareData($targetRule->getData(), $replace);
        /** @var TargetRule $originalTargetRule */
        $originalTargetRule = $fixtureFactory->createByCode('targetRule', $data);
        $originalTargetRule->persist();

        // Steps
        $filter = ['id' => $originalTargetRule->getRuleId()];
        $this->targetRuleIndex->open();
        $this->targetRuleIndex->getTargetRuleGrid()->searchAndOpen($filter);
        $this->targetRuleEdit->getPageActions()->delete();

        return ['sellingProducts' => [$sellingProduct]];
    }

    /**
     * Get data for replace in variations
     *
     * @param CatalogProductSimple $product
     * @param CatalogProductSimple $sellingProduct
     * @return array
     */
    protected function getReplaceData(CatalogProductSimple $product, CatalogProductSimple $sellingProduct)
    {
        return [
            'conditions_serialized' => [
                '%category_1%' => $product->getDataFieldConfig('category_ids')['source']->getIds()[0],
            ],
            'actions_serialized' => [
                '%category_2%' => $sellingProduct->getDataFieldConfig('category_ids')['source']->getIds()[0],
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
