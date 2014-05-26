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
 * Test Creation for CreateTargetRuleEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Test Category are created.
 * 2. Products are created (1 product per each category).
 *
 * Steps:
 * 1. Log in as default admin user.
 * 2. Go to Marketing > Related Products Rules
 * 3. Click 'Add Rule' button.
 * 4. Fill in data according to dataSet
 * 5. Save Related Products Rule.
 * 6. Perform all assertions.
 *
 * @group Target_Rules_(MX)
 * @ZephyrId MAGETWO-24686
 */
class CreateTargetRuleEntityTest extends Injectable
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
     * Run create TargetRule entity test
     *
     * @param CatalogProductSimple $product1
     * @param CatalogProductSimple $product2
     * @param TargetRule $targetRule
     * @param CustomerSegment|null $customerSegment
     * @return void
     */
    public function testCreateTargetRuleEntity(
        CatalogProductSimple $product1,
        CatalogProductSimple $product2,
        TargetRule $targetRule,
        CustomerSegment $customerSegment = null
    ) {
        // Preconditions:
        $product1->persist();
        $product2->persist();
        if ($customerSegment->hasData()) {
            $customerSegment->persist();
        }

        // Prepare data
        $replace = [
            'rule_information' => [
                'customer_segment_ids' => [
                    '%customer_segment%' => $customerSegment->hasData() ? $customerSegment->getName() : '',
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

        // Steps
        $this->targetRuleIndex->open();
        $this->targetRuleIndex->getGridPageActions()->addNew();
        $this->targetRuleNew->getTargetRuleForm()->fill($targetRule, null, $replace);
        $this->targetRuleNew->getPageActions()->save();
    }
}
