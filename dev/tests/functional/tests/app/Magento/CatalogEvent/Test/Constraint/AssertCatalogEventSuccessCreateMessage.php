<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CatalogEvent\Test\Constraint;

use Magento\CatalogEvent\Test\Page\Adminhtml\CatalogEventIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCatalogEventSuccessCreateMessage
 * Check present success message on Event page
 */
class AssertCatalogEventSuccessCreateMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    const SUCCESS_MESSAGE = 'You saved the event.';

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
