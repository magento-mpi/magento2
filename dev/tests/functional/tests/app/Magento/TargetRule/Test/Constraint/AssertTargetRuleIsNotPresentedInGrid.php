<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Test\Constraint; 

use Magento\TargetRule\Test\Fixture\TargetRule;
use Magento\TargetRule\Test\Page\Adminhtml\TargetRuleIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertTargetRuleIsNotPresentedInGrid
 */
class AssertTargetRuleIsNotPresentedInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that Target Rule is not presented in grid
     *
     * @param TargetRule $targetRule
     * @param TargetRuleIndex $targetRuleIndex
     * @return void
     */
    public function processAssert(
        TargetRule $targetRule,
        TargetRuleIndex $targetRuleIndex
    ) {
        $data = $targetRule->getData();
        $filter = [
            'id' => $data['id'],
            'name' => $data['name'],
        ];

        $targetRuleIndex->open();
        \PHPUnit_Framework_Assert::assertFalse(
            $targetRuleIndex->getTargetRuleGrid()->isRowVisible($filter),
            'Target rule with '
            . 'id \'' . $filter['id'] . '\', '
            . 'name \'' . $filter['name'] . '\', '
            . 'is presented in Target rule grid.'
        );
    }

    /**
     * Text success target rule is not presented in grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Target rule is not presented in grid.';
    }
}
