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
     * @param CheckoutAgreement $conditions
     * @return void
     */
    public function processAssert(CheckoutAgreementIndex $agreementIndex, CheckoutAgreement $conditions)
    {
        $filter = [
            'name' => $conditions->getName(),
        ];
        $agreementIndex->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $agreementIndex->getAgreementGridBlock()->isRowVisible($filter),
            'Checkout Agreement \'' . $conditions->getName() . '\' is not present in agreement grid.'
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
