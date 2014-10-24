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
 * Class AssertTermsInGrid
 * Check that checkout agreement is present in agreement grid
 */
class AssertTermsInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that checkout agreement is present in agreement grid
     *
     * @param CheckoutAgreementIndex $agreementIndex
     * @param CheckoutAgreement $agreement
     * @return void
     */
    public function processAssert(CheckoutAgreementIndex $agreementIndex, CheckoutAgreement $agreement)
    {
        $agreementIndex->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $agreementIndex->getAgreementGridBlock()->isRowVisible(['name' => $agreement->getName()]),
            'Checkout Agreement \'' . $agreement->getName() . '\' is absent in agreement grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Checkout Agreement is present in agreement grid.';
    }
}
