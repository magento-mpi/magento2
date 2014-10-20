<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CheckoutAgreements\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\CheckoutAgreements\Test\Page\Adminhtml\CheckoutAgreementIndex;

/**
 * Class AssertTermsSuccessDeleteMessage
 *Check that after deleting Term successful delete message appears.
 */
class AssertTermsSuccessDeleteMessage extends AbstractConstraint
{
    /**
     * Success terms and conditions delete message
     */
    const SUCCESS_DELETE_MESSAGE = 'The condition has been deleted.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that after deleting Term successful delete message appears.
     *
     * @param CheckoutAgreementIndex $agreementIndex
     * @return void
     */
    public function processAssert(CheckoutAgreementIndex $agreementIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_DELETE_MESSAGE,
            $agreementIndex->getMessagesBlock()->getSuccessMessages(),
            'Wrong success delete message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Terms and Conditions success delete message is present.';
    }
}
