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
 * Class AssertAgreementTextPresent
 * Check that agreement text present on Terms & Agreement page during install.
 */
class AssertAgreementTextPresent extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that part of license agreement text is present on Terms & Agreement page.
     *
     * @param Install $installPage
     * @return void
     */
    public function processAssert(Install $installPage)
    {
        \PHPUnit_Framework_Assert::assertContains(
            'Open Software License ("OSL") v. 3.0',
            $installPage->getLicenseBlock()->getLicense()
        );
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return "License agreement text is present on Terms & Agreement page";
    }
}
