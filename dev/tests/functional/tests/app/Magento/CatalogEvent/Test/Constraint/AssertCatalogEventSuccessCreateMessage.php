<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\CatalogEvent\Test\Page\Adminhtml\CatalogEventIndex;

/**
 * Class AssertCatalogEventSuccessCreateMessage
 * Check present success message on Event page
 */
class AssertCatalogEventSuccessCreateMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'You saved the event.';

    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Assert that message "You saved the event." is present on Event page
     *
     * @param CatalogEventIndex $catalogEventIndex
     * @return void
     */
    public function processAssert(CatalogEventIndex $catalogEventIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $catalogEventIndex->getMessagesBlock()->getSuccessMessages(),
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $catalogEventIndex->getMessagesBlock()->getSuccessMessages()
        );
    }

    /**
     * Text success present save message
     *
     * @return string
     */
    public function toString()
    {
        return 'Event success save message is present.';
    }
}
