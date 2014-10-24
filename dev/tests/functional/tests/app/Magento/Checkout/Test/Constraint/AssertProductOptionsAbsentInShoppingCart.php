<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Constraint;

use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Checkout\Test\Fixture\Cart;

/**
 * Class AssertProductOptionsAbsentInShoppingCart
 * Assert that cart item options for product(s) not display with old options.
 */
class AssertProductOptionsAbsentInShoppingCart extends AssertCartItemsOptions
{
    /**
     * Notice message.
     *
     * @var string
     */
    protected $notice = "\nForm data is equals to passed from fixture:\n";

    /**
     * Assert that cart item options for product(s) not display with old options.
     *
     * @param CheckoutCart $checkoutCart
     * @param Cart $deletedCart
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, Cart $deletedCart)
    {
        parent::processAssert($checkoutCart, $deletedCart);
    }

    /**
     * Verify form data not contains in fixture data.
     *
     * @param array $fixtureData
     * @param array $formData
     * @param bool $isStrict [optional]
     * @param bool $isPrepareError [optional]
     * @return array|string
     */
    protected function verifyContainsData(
        array $fixtureData,
        array $formData,
        $isStrict = false,
        $isPrepareError = true
    ) {
        $errors = [];

        foreach ($fixtureData as $key => $value) {
            if (in_array($key, $this->skippedFields)) {
                continue;
            }

            $formValue = isset($formData[$key]) ? $formData[$key] : null;
            if ($formValue && !is_array($formValue)) {
                $formValue = trim($formValue, '. ');
            }

            if (is_array($value) && is_array($formValue)) {
                $valueErrors = $this->verifyContainsData($value, $formValue, true, false);
                if (!empty($valueErrors)) {
                    $errors[$key] = $valueErrors;
                }
            } elseif (false !== strpos($fixtureData['value'], $formData['value'])) {
                if (is_array($value)) {
                    $value = $this->arrayToString($value);
                }
                if (is_array($formValue)) {
                    $formValue = $this->arrayToString($formValue);
                }
                $errors[] = sprintf('- %s: "%s" equals of "%s"', $key, $formValue, $value);
            }
        }

        if ($isStrict) {
            $diffData = array_diff(array_keys($formData), array_keys($fixtureData));
            if ($diffData) {
                $errors[] = '- fields ' . implode(', ', $diffData) . ' is absent in fixture';
            }
        }

        if ($isPrepareError) {
            return $this->prepareErrors($errors);
        }
        return $errors;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product options are absent in shopping cart.';
    }
}
