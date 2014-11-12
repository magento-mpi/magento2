<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Test\TestCase;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Magento\TargetRule\Test\Fixture\TargetRule;

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
class CreateTargetRuleEntityTest extends AbstractTargetRuleEntityTest
{
    /**
     * Run create TargetRule entity test
     *
     * @param CatalogProductSimple $product
     * @param CatalogProductSimple $relatedProduct
     * @param TargetRule $targetRule
     * @param CustomerSegment|null $customerSegment
     * @return array
     */
    public function testCreateTargetRuleEntity(
        CatalogProductSimple $product,
        CatalogProductSimple $relatedProduct,
        TargetRule $targetRule,
        CustomerSegment $customerSegment = null
    ) {
        // Preconditions:
        $product->persist();
        $relatedProduct->persist();
        if ($customerSegment->hasData()) {
            $customerSegment->persist();
        }
        $replace = $this->getReplaceData($product, $relatedProduct, $customerSegment);

        // Steps
        $this->targetRuleIndex->open();
        $this->targetRuleIndex->getGridPageActions()->addNew();
        $this->targetRuleNew->getTargetRuleForm()->fill($targetRule, null, $replace);
        $this->targetRuleNew->getPageActions()->save();

        // Prepare data for tear down
        $this->prepareTearDown($targetRule);

        return ['relatedProducts' => [$relatedProduct]];
    }
}
