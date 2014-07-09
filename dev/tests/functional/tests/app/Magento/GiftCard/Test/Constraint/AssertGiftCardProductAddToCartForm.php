<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\Constraint;

use Magento\GiftCard\Test\Fixture\GiftCardProduct;
use Magento\GiftCard\Test\Page\Product\CatalogProductView;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftCardProductAddToCartForm
 */
class AssertGiftCardProductAddToCartForm extends AbstractConstraint
{
    /**
     * Value for choose custom option
     */
    const CUSTOM_OPTION = 'custom';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that displayed data on product page(frontend) equals passed from fixture
     *
     * @param CatalogProductView $catalogProductView
     * @param GiftCardProduct $product
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, GiftCardProduct $product)
    {
        $catalogProductView->init($product);
        $catalogProductView->open();

        $giftcardAmounts = $product->hasData('giftcard_amounts') ? $product->getGiftcardAmounts() : [];
        $amountForm = $catalogProductView->getGiftCardBlock()->getAmountValues();
        $amountFixture = [];
        foreach ($giftcardAmounts as $amount) {
            $amountFixture[] = $amount['price'];
        }
        $amountDiff = array_diff($amountFixture, $amountForm);
        \PHPUnit_Framework_Assert::assertEmpty(
            $amountDiff,
            'Amount data on product page(frontend) not equals to passed from fixture.'
            . "\nFailed values: " . implode(', ', $amountDiff) . '.'
        );

        if (!empty($amountFixture)
            && $product->hasData('allow_open_amount')
            && 'Yes' == $product->getAllowOpenAmount()
        ) {
            \PHPUnit_Framework_Assert::assertContains(
                self::CUSTOM_OPTION,
                $amountForm,
                'Amount data on product page(frontend) not equals to passed from fixture.'
                . 'On product page(frontend) cannot choose custom amount.'
            );
        }

        $errors = $this->verifyFields($catalogProductView, $product);
        \PHPUnit_Framework_Assert::assertEmpty(
            $errors,
            "\nErrors fields: \n" . implode("\n", $errors)
        );
    }

    /**
     * Verify fields for "Add to cart" form
     *
     * @param CatalogProductView $catalogProductView
     * @param GiftCardProduct $product
     * @return array
     */
    protected function verifyFields(CatalogProductView $catalogProductView, GiftCardProduct $product)
    {
        $giftCard = $catalogProductView->getGiftCardBlock();
        $isAmountSelectVisible = $giftCard->isAmountSelectVisible();
        $isAmountInputVisible = $giftCard->isAmountInputVisible();
        $isAllowOpenAmount = $product->hasData('allow_open_amount') && 'Yes' == $product->getAllowOpenAmount();
        $isShowSelectAmount = $product->hasData('giftcard_amounts')
            && ($isAllowOpenAmount || 1 < count($product->getGiftcardAmounts()));
        $errors = [];

        // Prepare form
        if ($isAmountSelectVisible && $isAllowOpenAmount) {
            $giftCard->selectCustomAmount();
        }

        // Garbage errors
        if (!$isAmountSelectVisible && $isShowSelectAmount) {
            $errors[] = '- select amount is not displayed.';
        }
        if ($isAmountSelectVisible && !$isShowSelectAmount) {
            $errors[] = '- select amount is displayed.';
        }
        if ($isAllowOpenAmount && !$isAmountInputVisible) {
            $errors[] = '- input amount is not displayed.';
        }
        if (!$isAllowOpenAmount && $isAmountInputVisible) {
            $errors[] = '- input amount is displayed.';
        }
        if (!$giftCard->isSenderNameVisible()) {
            $errors[] = '- "Sender Name" is not displayed.';
        }
        if (!$giftCard->isRecipientNameVisible()) {
            $errors[] = '- "Recipient Name" is not displayed';
        }
        if ('Physical' != $product->getGiftcardType()) {
            if (!$giftCard->isSenderEmailVisible()) {
                $errors[] = '- "Sender Email" is not displayed';
            }
            if (!$giftCard->isRecipientEmailVisible()) {
                $errors[] = '- "Recipient Email" is not displayed';
            }
        }
        if (!$giftCard->isMessageVisible()) {
            $errors[] = '- "Message" is not displayed';
        }

        return $errors;
    }

    /**
     * Text success verify amount data on product page(frontend)
     *
     * @return string
     */
    public function toString()
    {
        return 'Displayed amount data on product page(frontend) equals to passed from fixture.';
    }
}
