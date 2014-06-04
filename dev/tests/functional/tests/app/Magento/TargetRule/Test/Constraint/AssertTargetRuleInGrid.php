<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\TargetRule\Test\Fixture\TargetRule;
use Magento\TargetRule\Test\Page\Adminhtml\TargetRuleIndex;

/**
 * Class AssertTargetRuleInGrid
 */
class AssertTargetRuleInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert target rule availability in Target Rule Grid
     *
     * @param TargetRule $targetRule
     * @param TargetRuleIndex $targetRuleIndex
     * @return void
     */
    public function processAssert(TargetRule $targetRule, TargetRuleIndex $targetRuleIndex)
    {
        $fromDate = $targetRule->hasData('from_date') ? strtotime($targetRule->getFromDate()) : null;
        $filter = [
            'name' => $targetRule->getName(),
            'applies_to' => $targetRule->getApplyTo(),
            'status' => $targetRule->getIsActive()
        ];
        if ($fromDate) {
            $filter['start_on_from'] = date('m/d/Y', $fromDate - 60*60*24);
        }

        $targetRuleIndex->open();
        $targetRuleIndex->getTargetRuleGrid()->search($filter);
        if ($fromDate) {
            $filter['start_on_from'] = date('M d, Y', $fromDate);
        }
        \PHPUnit_Framework_Assert::assertTrue(
            $targetRuleIndex->getTargetRuleGrid()->isRowVisible($filter, false),
            'Target rule with '
            . 'name \'' . $filter['name'] . '\', '
            . (isset($filter['start_on_from']) ? ('start_on_from \'' . $filter['start_on_from'] . '\', ') : '')
            . 'applies_to \'' . $filter['applies_to'] . '\', '
            . 'status \'' . $filter['status'] . '\', '
            . 'is absent in Target rule grid.'
        );
    }

    /**
     * Text success exist target rule in grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Target rule is present in grid.';
    }
}
