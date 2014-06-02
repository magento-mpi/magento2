<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Test\TestCase;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
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
     * Injection data
     *
     * @param TargetRuleIndex $targetRuleIndex
     * @param TargetRuleEdit $targetRuleEdit
     * @return void
     */
    public function __inject(
        TargetRuleIndex $targetRuleIndex,
        TargetRuleEdit $targetRuleEdit
    ) {
        $this->targetRuleIndex = $targetRuleIndex;
        $this->targetRuleEdit = $targetRuleEdit;
    }

    /**
     * Run delete TargetRule entity test
     *
     * @param CatalogProductSimple $product1
     * @param CatalogProductSimple $product2
     * @param TargetRule $targetRule
     * @return void
     */
    public function testDeleteTargetRuleEntity(
        CatalogProductSimple $product1,
        CatalogProductSimple $product2,
        TargetRule $targetRule
    ) {
        // Preconditions:
        $product1->persist();
        $product2->persist();
        $targetRule->persist();

        // Steps
        $filter = ['name' => $targetRule->getName()];
        $this->targetRuleIndex->open();
        $this->targetRuleIndex->getTargetRuleGrid()->searchAndOpen($filter);
        $this->targetRuleEdit->getPageActions()->delete();
    }
}
