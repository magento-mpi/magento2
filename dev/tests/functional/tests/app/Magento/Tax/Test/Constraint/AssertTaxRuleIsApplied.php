<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Constraint;

use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Customer\Test\Fixture\AddressInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Tax\Test\Fixture\TaxRule;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\FixtureFactory;

/**
 * Class AssertTaxRuleIsApplied
 */
class AssertTaxRuleIsApplied extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that tax rule is applied on product in shopping cart.
     *
     * @param FixtureFactory $fixtureFactory
     * @param TaxRule $taxRule
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountLogout $customerAccountLogout
     * @param CustomerInjectable $customer
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param AddressInjectable $address
     * @param array $shipping
     * @return void
     */
    public function processAssert(
        FixtureFactory $fixtureFactory,
        TaxRule $taxRule,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountLogout $customerAccountLogout,
        CustomerInjectable $customer,
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        AddressInjectable $address,
        array $shipping
    ) {
        $errorMessages = [];
        // Creating simple product with custom tax class
        /** @var \Magento\Tax\Test\Fixture\TaxClass $taxProductClass */
        $taxProductClass = $taxRule->getDataFieldConfig('tax_product_class')['source']->getFixture()[0];
        /** @var \Magento\Catalog\Test\Fixture\CatalogProductSimple $productSimple */
        $productSimple = $fixtureFactory->createByCode(
            'catalogProductSimple',
            [
                'dataSet' => '100_dollar_product',
                'data' => [
                    'tax_class_id' => ['tax_product_class' => $taxProductClass],
                ]
            ]
        );
        $productSimple->persist();
        // Customer login
        $customerAccountLogout->open();
        $customerAccountLogin->open();
        $customerAccountLogin->getLoginBlock()->login($customer);
        // Clearing shopping cart and adding product to shopping cart
        $checkoutCart->open()->getCartBlock()->clearShoppingCart();
        $catalogProductView->init($productSimple);
        $catalogProductView->open();
        $catalogProductView->getViewBlock()->clickAddToCart();
        // Estimate Shipping and Tax
        $checkoutCart->getShippingBlock()->openEstimateShippingAndTax();
        $checkoutCart->getShippingBlock()->fill($address);
        $checkoutCart->getShippingBlock()->clickGetQuote();
        $checkoutCart->getShippingBlock()->selectShippingMethod($shipping);
        // Preparing data to compare
        $taxRate = $taxRule->getDataFieldConfig('tax_rate')['source']->getFixture()[0]->getRate();
        $expectedGrandTotal = $productSimple->getPrice() + $taxRate + $shipping['price'];
        $expectedGrandTotal = number_format($expectedGrandTotal, 2);
        $actualGrandTotal = $checkoutCart->getTotalsBlock()->getGrandTotal();

        if ($checkoutCart->getTotalsBlock()->isTaxVisible()) {
            $expectedTax = number_format($taxRate, 2);
            $actualTax = $checkoutCart->getTotalsBlock()->getTax();
            if ($expectedTax !== $actualTax) {
                $errorMessages[] = 'Tax is not correct.'
                    . "\nExpected: " . $expectedTax
                    . "\nActual: " . $actualTax;
            }
        }
        if ($expectedGrandTotal !== $actualGrandTotal) {
            $errorMessages[] = 'Grand Total is not correct.'
                . "\nExpected: " . $expectedGrandTotal
                . "\nActual: " . $actualGrandTotal;
        }

        \PHPUnit_Framework_Assert::assertTrue(
            empty($errorMessages),
            implode(";\n", $errorMessages)
        );
    }

    /**
     * Text of Tax Rule is applied on product in shopping cart.
     *
     * @return string
     */
    public function toString()
    {
        return "Tax rule applied on product in shopping cart.";
    }
}
