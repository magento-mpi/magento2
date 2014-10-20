<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CheckoutAgreements\Test\Constraint;

use Magento\CheckoutAgreements\Test\Fixture\CheckoutAgreement;
use Magento\CheckoutAgreements\Test\Page\Adminhtml\CheckoutAgreementIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertTermsAbsentInGrid
 * Check that checkout agreement is absent in agreement grid.
 */
class AssertTermsAbsentInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that checkout agreement is absent in agreement grid.
     *
     * @param CheckoutAgreementIndex $agreementIndex
     * @param CheckoutAgreement|null $agreement
     * @return void
     */
    public function processAssert(CheckoutAgreementIndex $agreementIndex, CheckoutAgreement $agreement)
    {
        $agreementIndex->open();
        \PHPUnit_Framework_Assert::assertFalse(
            $agreementIndex->getAgreementGridBlock()->isRowVisible(['name' => $agreement->getName()]),
            'Checkout Agreement \'' . $agreement->getName() . '\' is present in agreement grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Checkout Agreement is absent in agreement grid.';
    }
}
