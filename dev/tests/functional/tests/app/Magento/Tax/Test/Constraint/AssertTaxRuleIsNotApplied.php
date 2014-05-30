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
     * @param FixtureFactory $fixtureFactory
     * @param TaxRule $taxRule
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountLogout $customerAccountLogout
     * @param CustomerInjectable $customer
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param AddressInjectable $address
     * @param array $shipping
     * @param TaxRule $initialTaxRule
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
        array $shipping,
        TaxRule $initialTaxRule = null
    ) {
        $errorMessages = [];
        if ($initialTaxRule !== null) {
            $taxRuleCode = ($taxRule->hasData('code')) ? $taxRule->getCode() : $initialTaxRule->getCode();
        } else {
            $taxRuleCode = $taxRule->getCode();
        }
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
        $expectedGrandTotal = $productSimple->getPrice() + $shipping['price'];
        $expectedGrandTotal = number_format($expectedGrandTotal, 2);
        $actualGrandTotal = $checkoutCart->getTotalsBlock()->getGrandTotal();

        if ($checkoutCart->getTotalsBlock()->isTaxVisible()) {
            $errorMessages[] = 'Tax Rule \'' . $taxRuleCode . '\' present in shopping cart.';
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
     * Text of Tax Rule is not applied on product in shopping cart.
     *
     * @return string
     */
    public function toString()
    {
        return "Tax rule was not applied on product in shopping cart.";
    }
}
