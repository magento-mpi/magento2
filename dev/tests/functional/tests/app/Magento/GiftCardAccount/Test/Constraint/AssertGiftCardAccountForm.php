<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Mtf\Constraint\AbstractAssertForm;
use Magento\GiftCardAccount\Test\Page\Adminhtml\Index;
use Magento\GiftCardAccount\Test\Page\Adminhtml\NewIndex;
use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;

/**
 * Class AssertGiftCardAccountForm
 * Assert that gift card account equals to passed from fixture
 */
class AssertGiftCardAccountForm extends AbstractAssertForm
{
    /**
     * Skipped fields for verify data
     *
     * @var array
     */
    protected $skippedFields = ['code'];

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that gift card account equals to passed from fixture
     *
     * @param GiftCardAccount $giftCardAccount
     * @param Index $index
     * @param NewIndex $newIndex
     * @return void
     */
    public function processAssert(
        GiftCardAccount $giftCardAccount,
        Index $index,
        NewIndex $newIndex
    ) {
        $index->open();
        $filter = ['balance' => $giftCardAccount->getBalance()];
        $index->getGiftCardAccount()->searchAndOpen($filter, false);

        $giftCardAccountFormData = $newIndex->getPageMainForm()->getData();
        $dataDiff = $this->verifyData($giftCardAccount->getData(), $giftCardAccountFormData);

        \PHPUnit_Framework_Assert::assertEmpty(
            $dataDiff,
            'Gift card account form data does not equal to passed from fixture.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift card account form data equals to passed from fixture.';
    }
}
