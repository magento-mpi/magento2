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
    public function prepareTearDown(
        TargetRule $targetRule
    ) {
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
}
