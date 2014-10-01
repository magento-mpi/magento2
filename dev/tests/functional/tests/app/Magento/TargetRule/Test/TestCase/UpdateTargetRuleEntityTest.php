<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Test\TestCase;

use Magento\TargetRule\Test\Fixture\TargetRule;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;

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
class UpdateTargetRuleEntityTest extends TargetRuleEntityTest
{
    /**
     * Run update TargetRule entity test
     *
     * @param CatalogProductSimple $product
     * @param CatalogProductSimple $sellingProduct
     * @param TargetRule $initialTargetRule
     * @param TargetRule $targetRule
     * @param CustomerSegment|null $customerSegment
     * @return array
     */
    public function testUpdateTargetRuleEntity(
        CatalogProductSimple $product,
        CatalogProductSimple $sellingProduct,
        TargetRule $initialTargetRule,
        TargetRule $targetRule,
        CustomerSegment $customerSegment = null
    ) {
        // Preconditions:
        $product->persist();
        $sellingProduct->persist();
        $initialTargetRule->persist();
        if ($customerSegment->hasData()) {
            $customerSegment->persist();
        }
        $replace = $this->getReplaceData($product, $sellingProduct, $customerSegment);

        // Steps
        $filter = ['name' => $initialTargetRule->getName()];
        $this->targetRuleIndex->open();
        $this->targetRuleIndex->getTargetRuleGrid()->searchAndOpen($filter);
        $this->targetRuleNew->getTargetRuleForm()->fill($targetRule, null, $replace);
        $this->targetRuleNew->getPageActions()->save();

        // Prepare data for tear down
        $this->prepareTearDown($targetRule);

        return ['sellingProducts' => [$sellingProduct]];
    }
}
