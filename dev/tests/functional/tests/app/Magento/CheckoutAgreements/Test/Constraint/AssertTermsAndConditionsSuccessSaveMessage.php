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
 * Class AssertTermsAndConditionsSuccessSaveMessage
 * Check that after save block successful message appears
 */
class AssertTermsAndConditionsSuccessSaveMessage extends AbstractConstraint
{
    const SUCCESS_SAVE_MESSAGE = 'The condition has been saved.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that after save block successful message appears
     *
     * @param CheckoutAgreementIndex $agreementIndex
     * @return void
     */
    public function processAssert(CheckoutAgreementIndex $agreementIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_SAVE_MESSAGE,
            $agreementIndex->getMessagesBlock()->getSuccessMessages(),
            'Wrong success message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Terms and Conditions success create message is present.';
    }
}
