<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\CatalogRule\Test\Page;

/**
 * Class AssertCatalogRuleSaveMessage
 *
 * @package Magento\CatalogRule\Test\Constraint
 */
class AssertCatalogRuleSaveMessage extends AbstractConstraint
{
    /**
     * Catalog rule is saved message
     */
    const SUCCESS_MESSAGE = 'The rule has been saved.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success message is displayed after Catalog Rule save
     *
     * @param Page\CatalogRule $catalogRuleInGrid
     * @return void
     */
    public function processAssert(Page\CatalogRule $catalogRuleInGrid)
    {
        $catalogRuleInGrid->open();
        $actualMessage = $catalogRuleInGrid->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * @inheritdoc
     */
    public function toString()
    {
        return 'Catalog Rule success save message is present.';
    }
}
