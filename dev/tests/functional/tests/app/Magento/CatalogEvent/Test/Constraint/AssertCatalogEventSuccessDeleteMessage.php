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
 * Check present delete message on Event page
 */
class AssertCatalogEventSuccessDeleteMessage extends AbstractConstraint
{
    const DELETE_MESSAGE = 'You deleted the event.';

    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Assert that message "You deleted the event." is present on Event page
     *
     * @param CatalogEventIndex $catalogEventIndex
     * @return void
     */
    public function processAssert(CatalogEventIndex $catalogEventIndex)
    {

        \PHPUnit_Framework_Assert::assertEquals(
            self::DELETE_MESSAGE,
            $catalogEventIndex->getMessagesBlock()->getSuccessMessages(),
            'Wrong message is displayed.'
            . "\nExpected: " . self::DELETE_MESSAGE
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
        return 'Event delete message is present.';
    }
}
