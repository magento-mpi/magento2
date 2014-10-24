<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Constraint;

use Mtf\Constraint\AbstractAssertForm;
use Magento\Checkout\Test\Page\CheckoutCart;
use Mtf\Fixture\FixtureInterface;
use Magento\Checkout\Test\Fixture\Cart;
use Magento\Checkout\Test\Fixture\Cart\Items;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertCartItemsOptions
 * Assert that cart item options for product(s) display with correct information block
 *
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class AssertCartItemsOptions extends AbstractAssertForm
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that cart item options for product(s) display with correct information block
     * (custom options, variations, links, samples, bundle items etc) according to passed from dataSet
     *
     * @param CheckoutCart $checkoutCart
     * @param Cart $cart
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, Cart $cart)
    {
        $checkoutCart->open();
        /** @var Items $sourceProducts */
        $sourceProducts = $cart->getDataFieldConfig('items')['source'];
        $products = $sourceProducts->getProducts();
        $items = $cart->getItems();
        $productsData = [];
        $cartData = [];

        foreach ($items as $key => $item) {
            /** @var CatalogProductSimple $product */
            $product = $products[$key];
            $productName = $product->getName();
            /** @var FixtureInterface $item */
            $checkoutItem = $item->getData();
            $cartItem = $checkoutCart->getCartBlock()->getCartItem($product);

            $productsData[$productName] = [
                'options' => $this->sortDataByPath($checkoutItem['options'], '::title')
            ];
            $cartData[$productName] = [
                'options' => $this->sortDataByPath($cartItem->getOptions(), '::title')
            ];
        }

        $error = $this->verifyContainsData($productsData, $cartData, true);
        \PHPUnit_Framework_Assert::assertEmpty($error, $error);
    }

    /**
     * Verify form data contains in fixture data
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

            if (null === $formValue) {
                $errors[] = '- field "' . $key . '" is absent in form';
            } elseif (is_array($value) && is_array($formValue)) {
                $valueErrors = $this->verifyContainsData($value, $formValue, true, false);
                if (!empty($valueErrors)) {
                    $errors[$key] = $valueErrors;
                }
            } elseif (null === strpos($value, $formValue)) {
                if (is_array($value)) {
                    $value = $this->arrayToString($value);
                }
                if (is_array($formValue)) {
                    $formValue = $this->arrayToString($formValue);
                }
                $errors[] = sprintf('- %s: "%s" instead of "%s"', $key, $formValue, $value);
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
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Product options on the page match.';
    }
}
