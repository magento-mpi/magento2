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
 * Class AssertTaxRuleIsNotApplied
 */
class AssertTaxRuleIsNotApplied extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that tax rule is not applied on product in shopping cart.
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
     * @param TaxRule $initialTaxRule
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
        $shipping,
        TaxRule $initialTaxRule = null
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

        $isTaxVisible = $checkoutCart->getTotalsBlock()->isTaxVisible();
        if ($initialTaxRule !== null) {
            $taxRuleCode = ($taxRule->hasData('code')) ? $taxRule->getCode() : $initialTaxRule->getCode();
        } else {
            $taxRuleCode = $taxRule->getCode();
        }
        \PHPUnit_Framework_Assert::assertFalse(
            $isTaxVisible,
            'Tax Rule \'' . $taxRuleCode . '\' present in shopping cart.'
        );

        $expectedGrandTotal = $productSimple->getPrice() + $shipping['price'];
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
     * Text of Tax Rule is not applied on product in shopping cart.
     *
     * @return string
     */
    public function toString()
    {
        return "Tax rule was not applied on product in shopping cart.";
    }
}
