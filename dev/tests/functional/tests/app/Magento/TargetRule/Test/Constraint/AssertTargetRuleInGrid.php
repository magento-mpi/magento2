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
     * Day in seconds
     */
    const DAY = 86400;

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
     * @param TargetRule|null $initialTargetRule
     * @return void
     */
    public function processAssert(
        TargetRule $targetRule,
        TargetRuleIndex $targetRuleIndex,
        TargetRule $initialTargetRule = null
    ) {
        $data = $initialTargetRule
            ? array_replace($initialTargetRule->getData(), $targetRule->getData())
            : $targetRule->getData();
        $fromDate = isset($data['from_date']) ? strtotime($data['from_date']) : null;
        $filter = [
            'name' => $data['name'],
            'applies_to' => $data['apply_to'],
            'status' => $data['is_active']
        ];
        if ($fromDate) {
            $filter['start_on_from'] = date('m/d/Y', $fromDate - self::DAY);
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
