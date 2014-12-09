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
 * Check that agreement text present on Terms & Agreement page during install.
 */
class AssertAgreementTextPresent extends AbstractConstraint
{
    /**
     * Part of license agreement text.
     */
    const LICENSE_AGREEMENT_TEXT = 'Open Software License ("OSL") v. 3.0';

    /**
     * Constraint severeness.
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
            self::LICENSE_AGREEMENT_TEXT,
            $installPage->getLicenseBlock()->getLicense(),
            'License agreement text is absent.'
        );
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return "License agreement text is present on Terms & Agreement page.";
    }
}
