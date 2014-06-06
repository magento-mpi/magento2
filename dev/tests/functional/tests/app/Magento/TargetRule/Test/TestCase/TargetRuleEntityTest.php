<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\TargetRule\Test\Page\Adminhtml\TargetRuleEdit;
use Magento\TargetRule\Test\Page\Adminhtml\TargetRuleIndex;
use Magento\TargetRule\Test\Page\Adminhtml\TargetRuleNew;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Magento\TargetRule\Test\Fixture\TargetRule;

/**
 * Class TargetRuleEntityTest
 * Parent class for TargetRule tests.
 */
abstract class TargetRuleEntityTest extends Injectable
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
     * @var TargetRuleEdit
     */
    protected $targetRuleEdit;

    /**
     * @var TargetRule
     */
    protected $targetRule;

    /**
     * Injection data
     *
     * @param TargetRuleIndex $targetRuleIndex
     * @param TargetRuleNew $targetRuleNew
     * @param TargetRuleEdit $targetRuleEdit
     * @return void
     */
    public function __inject(
        TargetRuleIndex $targetRuleIndex,
        TargetRuleNew $targetRuleNew,
        TargetRuleEdit $targetRuleEdit
    ) {
        $this->targetRuleIndex = $targetRuleIndex;
        $this->targetRuleNew = $targetRuleNew;
        $this->targetRuleEdit = $targetRuleEdit;
    }

    /**
     * Prepare data for tear down
     *
     * @param TargetRule $targetRule
     * @return void
     */
    public function prepareTearDown(TargetRule $targetRule)
    {
        $this->targetRule = $targetRule;
    }

    /**
     * Clear data after test
     *
     * @return void
     */
    public function tearDown()
    {
        if (!$this->targetRule instanceof TargetRule) {
            return;
        }
        $filter = ['name' => $this->targetRule->getName()];
        $this->targetRuleIndex->open();
        $this->targetRuleIndex->getTargetRuleGrid()->searchAndOpen($filter);
        $this->targetRuleEdit->getPageActions()->delete();
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
