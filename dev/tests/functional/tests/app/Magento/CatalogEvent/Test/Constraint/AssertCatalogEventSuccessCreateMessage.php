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
 *
 * @package Magento\CatalogEvent\Test\Constraint
 */
class AssertCatalogEventSuccessCreateMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'You saved the event.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that message "You saved the event." is presented on Events page
     *
     * @param CatalogEventIndex $CatalogEventIndex
     * @return void
     */
    public function processAssert(CatalogEventIndex $CatalogEventIndex)
    {
        $actualMessage = $CatalogEventIndex->getMessageBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $actualMessage
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
