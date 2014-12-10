<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Install\Test\Constraint;

use Magento\Install\Test\Page\Install;
use Mtf\Constraint\AbstractConstraint;

/**
 * Check that system can successfully connect to DB.
 */
class AssertSuccessDbConnection extends AbstractConstraint
{
    /**
     * Successful connection message.
     */
    const SUCCESSFUL_CONNECTION = 'Test connection successful.';

    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that system can successfully connect to DB.
     *
     * @param Install $installPage
     * @return void
     */
    public function processAssert(Install $installPage)
    {
        $installPage->getDatabaseBlock()->clickTestConnection();
        \PHPUnit_Framework_Assert::assertContains(
            self::SUCCESSFUL_CONNECTION,
            $installPage->getDatabaseBlock()->getSuccessConnectionMessage(),
            'Unable to connect to database.'
        );
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return "System successfully connected to DB.";
    }
}
