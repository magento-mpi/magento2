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
 * Class AssertSuccessfulCheck
 * Check that PHP Version, PHP Extensions and File Permission are ok.
 */
class AssertSuccessfulCheck extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that PHP Version, PHP Extensions and File Permission are ok.
     *
     * @param Install $installPage
     * @return void
     */
    public function processAssert(Install $installPage)
    {
        \PHPUnit_Framework_Assert::assertContains(
            'Completed!',
            $installPage->getReadinessBlock()->getCompletedMessage()
        );
        \PHPUnit_Framework_Assert::assertContains(
            'Your PHP version is correct',
            $installPage->getReadinessBlock()->getPhpVersionCheck()
        );
        \PHPUnit_Framework_Assert::assertContains(
            'You meet 2 out of 2 PHP extensions requirements.',
            $installPage->getReadinessBlock()->getPhpExtensionsCheck()
        );
        \PHPUnit_Framework_Assert::assertContains(
            'You meet 4 out of 4 writable file permission requirements.',
            $installPage->getReadinessBlock()->getFilePermissionCheck()
        );
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return "PHP Version, PHP Extensions and File Permission are ok";
    }
}
