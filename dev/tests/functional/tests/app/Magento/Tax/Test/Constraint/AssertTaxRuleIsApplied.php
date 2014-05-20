<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Customer\Test\Fixture\AddressInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Tax\Test\Fixture\TaxRule;
use Mtf\Constraint\AbstractConstraint;

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
     * @param TaxRule $taxRule
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountLogout $customerAccountLogout
     * @param CustomerInjectable $customer
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductSimple $productSimple
     * @param CheckoutCart $checkoutCart
     * @param AddressInjectable $address
     * @param $shipping
     * @return void
     */
    public function processAssert(
        TaxRule $taxRule,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountLogout $customerAccountLogout,
        CustomerInjectable $customer,
        CatalogProductView $catalogProductView,
        CatalogProductSimple $productSimple,
        CheckoutCart $checkoutCart,
        AddressInjectable $address,
        $shipping
    ) {
        $customerAccountLogin->open();
        $customerAccountLogin->getLoginBlock()->login($customer);
        $checkoutCart->open()->getCartBlock()->clearShoppingCart();
        $catalogProductView->init($productSimple);
        $catalogProductView->open();
        $catalogProductView->getViewBlock()->clickAddToCart();
        $checkoutCart->getShippingBlock()->openEstimateShippingAndTax();
        $checkoutCart->getShippingBlock()->fill($address);
        $checkoutCart->getShippingBlock()->getQuote();
        $checkoutCart->getShippingBlock()->selectShippingMethod($shipping);

        $taxRate = $taxRule->getDataFieldConfig('tax_rate')['source']->getFixture()[0];
        $taxRate = $taxRate->getRate();

        $isTaxVisible = $checkoutCart->getTotalsBlock()->isTaxVisible();
        if ($isTaxVisible) {
            $expectedTax = '$' . number_format($taxRate, 2);
            $actualTax = $checkoutCart->getTotalsBlock()->getTax();
            \PHPUnit_Framework_Assert::assertEquals(
                $expectedTax,
                $actualTax,
                'Tax is not correct.'
                . "\nExpected: " . $expectedTax
                . "\nActual: " . $actualTax
            );
        }

        $expectedGrandTotal = $productSimple->getPrice() + $taxRate + $shipping['price'];
        $expectedGrandTotal = '$' . number_format($expectedGrandTotal, 2);
        $actualGrandTotal = $checkoutCart->getTotalsBlock()->getGrandTotal();
        \PHPUnit_Framework_Assert::assertEquals(
            $expectedGrandTotal,
            $actualGrandTotal,
            'Grand Total is not correct.'
            . "\nExpected: " . $expectedGrandTotal
            . "\nActual: " . $actualGrandTotal
        );
        $customerAccountLogout->open();
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
