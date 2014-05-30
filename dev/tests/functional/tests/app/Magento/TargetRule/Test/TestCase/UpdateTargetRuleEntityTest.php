<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Magento\TargetRule\Test\Fixture\TargetRule;
use Magento\TargetRule\Test\Page\Adminhtml\TargetRuleIndex;
use Magento\TargetRule\Test\Page\Adminhtml\TargetRuleNew;

/**
 * Test Creation for UpdateTargetRuleEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Target Rule is created.
 *
 * Steps:
 * 1. Login to backend.
 * 2. Navigate to MARKETING > Related Products Rules.
 * 3. Click Target Rule from grid.
 * 4. Edit test value(s) according to dataset.
 * 5. Click 'Save' button.
 * 6. Perform all asserts.
 *
 * @group Target_Rules_(MX)
 * @ZephyrId MAGETWO-24807
 */
class UpdateTargetRuleEntityTest extends Injectable
{
    /**
     * @var TargetRuleIndex
     */
    protected $targetRuleIndex;

    /**
     * @var TargetRuleNew
     */
    protected $targetRuleNew;

    /**
     * Injection data
     *
     * @param TargetRuleIndex $targetRuleIndex
     * @param TargetRuleNew $targetRuleNew
     */
    public function __inject(
        TargetRuleIndex $targetRuleIndex,
        TargetRuleNew $targetRuleNew
    ) {
        $this->targetRuleIndex = $targetRuleIndex;
        $this->targetRuleNew = $targetRuleNew;
    }

    /**
     * Run update TargetRule entity test
     *
     * @param CatalogProductSimple $product1
     * @param CatalogProductSimple $product2
     * @param TargetRule $initialTargetRule
     * @param TargetRule $targetRule
     * @param CustomerSegment|null $customerSegment
     * @return void
     */
    public function testUpdateTargetRuleEntity(
        CatalogProductSimple $product1,
        CatalogProductSimple $product2,
        TargetRule $initialTargetRule,
        TargetRule $targetRule,
        CustomerSegment $customerSegment = null
    ) {
        // Preconditions:
        $product1->persist();
        $product2->persist();
        $initialTargetRule->persist();
        if ($customerSegment->hasData()) {
            $customerSegment->persist();
        }
        $replace = $this->getReplaceData($product1, $product2, $customerSegment);

        // Steps
        $filter = ['name' => $initialTargetRule->getName()];
        $this->targetRuleIndex->open();
        $this->targetRuleIndex->getTargetRuleGrid()->searchAndOpen($filter);
        $this->targetRuleNew->getTargetRuleForm()->fill($targetRule, null, $replace);
        $this->targetRuleNew->getPageActions()->save();
    }

    /**
     * Get data for replace in variations
     *
     * @param CatalogProductSimple $product1
     * @param CatalogProductSimple $product2
     * @param CustomerSegment|null $customerSegment
     * @return array
     */
    protected function getReplaceData(
        CatalogProductSimple $product1,
        CatalogProductSimple $product2,
        CustomerSegment $customerSegment = null
    ) {
        $customerSegmentName = ($customerSegment && $customerSegment->hasData()) ? $customerSegment->getName() : '';
        return [
            'rule_information' => [
                'customer_segment_ids' => [
                    '%customer_segment%' => $customerSegmentName,
                ],
            ],
            'products_to_match' => [
                'conditions_serialized' => [
                    '%category_1%' => $product1->getCategoryIds()[0]['id'],
                ],
            ],
            'products_to_display' => [
                'actions_serialized' => [
                    '%category_2%' => $product2->getCategoryIds()[0]['id'],
                ],
            ],
        ];
    }
}
