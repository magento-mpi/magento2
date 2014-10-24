<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reminder\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Reminder\Test\Page\Adminhtml\ReminderIndex;
use Magento\Reminder\Test\Fixture\Reminder;

/**
 * Assert reminder present in grid.
 */
class AssertReminderInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert reminder present in grid:
     * - Rule
     * - Start on
     * - End on
     * - Status
     * - Website
     *
     * @param ReminderIndex $reminderIndex
     * @param Reminder $reminder
     * @return void
     */
    public function processAssert(ReminderIndex $reminderIndex, Reminder $reminder)
    {
        $websiteIds = $reminder->getWebsiteIds();
        $filter = array_filter([
            'name' => $reminder->getName(),
            'from_date_to' => $reminder->getFromDate(),
            'to_date_to' => $reminder->getToDate(),
            'status' => $reminder->getIsActive(),
            'website' => is_array($websiteIds) ? reset($websiteIds) : null
        ]);

        $reminderIndex->open();
        $reminderIndex->getGrid()->search($filter);

        $filter['from_date_to'] = date('M d, Y', strtotime($filter['from_date_to']));
        $filter['to_date_to'] = date('M d, Y', strtotime($filter['to_date_to']));
        \PHPUnit_Framework_Assert::assertTrue(
            $reminderIndex->getGrid()->isRowVisible($filter, false, false),
            'Reminder with '
            . 'name \'' . $filter['name'] . '\', '
            . 'is absent in grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Reminder present in grid.';
    }
}
