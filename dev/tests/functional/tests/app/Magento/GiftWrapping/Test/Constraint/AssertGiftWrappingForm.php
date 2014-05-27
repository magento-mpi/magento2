<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Test\Constraint;

use Magento\GiftWrapping\Test\Fixture\GiftWrapping;
use Magento\GiftWrapping\Test\Page\Adminhtml\GiftWrappingIndex;
use Magento\GiftWrapping\Test\Page\Adminhtml\GiftWrappingNew;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftWrappingForm
 */
class AssertGiftWrappingForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that Gift Wrapping form was filled correctly
     *
     * @param GiftWrappingIndex $giftWrappingIndexPage
     * @param GiftWrappingNew $giftWrappingNewPage
     * @param GiftWrapping $giftWrapping
     * @return void
     */
    public function processAssert(
        GiftWrappingIndex $giftWrappingIndexPage,
        GiftWrappingNew $giftWrappingNewPage,
        GiftWrapping $giftWrapping
    ) {
        $data = $giftWrapping->getData();
        $data['base_price'] = number_format($data['base_price'], 2);
        $filter = [
            'design' => $giftWrapping->getDesign(),
            'status' => $giftWrapping->getStatus(),
        ];
        $giftWrappingIndexPage->open();
        $giftWrappingIndexPage->getGiftWrappingGrid()->searchAndOpen($filter);
        $formData = $giftWrappingNewPage->getGiftWrappingForm()->getData($giftWrapping);
        $dataDiff = $this->verifyForm($formData, $data);
        \PHPUnit_Framework_Assert::assertTrue(
            empty($dataDiff),
            'Gift Wrapping form was filled incorrectly.'
            . "\nLog:\n" . implode(";\n", $dataDiff)
        );
    }

    /**
     * Verifying that form is filled correctly
     *
     * @param array $formData
     * @param array $fixtureData
     * @return array $errorMessage
     */
    protected function verifyForm(array $formData, array $fixtureData)
    {
        $errorMessage = [];

        foreach ($fixtureData as $key => $value) {
            if (is_array($value)) {
                $diff = array_diff($value, $formData[$key]);
                $diff = array_merge($diff, array_diff($formData[$key], $value));
                if (!empty($diff)) {
                    $errorMessage[] = "Data in " . $key . " field not equal."
                        . "\nExpected: " . implode(", ", $value)
                        . "\nActual: " . implode(", ", $formData[$key]);
                }
            } else {
                if ($value !== $formData[$key]) {
                    $errorMessage[] = "Data in " . $key . " field not equal."
                        . "\nExpected: " . $value
                        . "\nActual: " . $formData[$key];
                }
            }
        }

        return $errorMessage;
    }

    /**
     * Text that form was filled correctly
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift Wrapping form was filled correctly.';
    }
}
