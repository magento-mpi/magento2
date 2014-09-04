<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Test\Constraint;

use Mtf\Constraint\AbstractAssertForm;
use Magento\GiftWrapping\Test\Fixture\GiftWrapping;
use Magento\GiftWrapping\Test\Page\Adminhtml\GiftWrappingIndex;
use Magento\GiftWrapping\Test\Page\Adminhtml\GiftWrappingNew;

/**
 * Class AssertGiftWrappingMassActionForm
 * Assert that mass action Gift Wrapping form was filled correctly
 */
class AssertGiftWrappingMassActionForm extends AbstractAssertForm
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Skipped fields while verifying
     *
     * @var array
     */
    protected $skippedFields = [
        'wrapping_id',
    ];

    /**
     * @param GiftWrappingIndex $giftWrappingIndexPage
     * @param GiftWrappingNew $giftWrappingNewPage
     * @param array $giftWrappingsModified
     * @param string $status
     * @return void
     */
    public function processAssert(
        GiftWrappingIndex $giftWrappingIndexPage,
        GiftWrappingNew $giftWrappingNewPage,
        array $giftWrappingsModified,
        $status
    ) {
        $errors = [];
        foreach ($giftWrappingsModified as $giftWrappingModified) {
            $data = $giftWrappingModified->getData();
            $data['base_price'] = number_format($data['base_price'], 2);
            $data['status'] = $status === '-' ? $data['status'] : $status;
            $filter = ['design' => $data['design']];
            $giftWrappingIndexPage->open();
            $giftWrappingIndexPage->getGiftWrappingGrid()->searchAndOpen($filter);
            $formData = $giftWrappingNewPage->getGiftWrappingForm()->getData();
            $errors = $this->verifyData($formData, $data);
        }
        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift Wrapping form was filled correctly.';
    }
}
