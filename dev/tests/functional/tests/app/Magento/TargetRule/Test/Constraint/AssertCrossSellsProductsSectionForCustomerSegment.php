<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\TargetRule\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Checkout\Test\Page\CheckoutCart;
use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\InjectableFixture;
use Mtf\ObjectManager;

/**
 * Class AssertCrossSellsProductsSectionForCustomerSegment
 * Assert that product is displayed in cross-sell section for customer segment
 */
class AssertCrossSellsProductsSectionForCustomerSegment extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'medium';
    /* end tags */

    /**
     * Assert that product is displayed in cross-sell section for customer segment
     *
     * @param Browser $browser
     * @param ObjectManager $objectManager
     * @param FixtureFactory $fixtureFactory
     * @param CheckoutCart $checkoutCart
     * @param CatalogProductSimple $product
     * @param CatalogProductView $catalogProductView
     * @param InjectableFixture[] $relatedProducts
     * @return void
     */
    public function processAssert(
        Browser $browser,
        ObjectManager $objectManager,
        FixtureFactory $fixtureFactory,
        CheckoutCart $checkoutCart,
        CatalogProductSimple $product,
        CatalogProductView $catalogProductView,
        array $relatedProducts
    ) {
        // Create customer and login for test customer segment
        $customer = $fixtureFactory->createByCode('customerInjectable', ['dataSet' => 'default']);
        $customer->persist();
        $loginCustomerOnFrontendStep = $objectManager->create(
            '\Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        );
        $loginCustomerOnFrontendStep->run();

        // Clear cart
        $checkoutCart->open();
        $checkoutCart->getCartBlock()->clearShoppingCart();

        // Check display cross sell products
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $catalogProductView->getViewBlock()->addToCart($product);
        $errors = [];
        foreach ($relatedProducts as $relatedProduct) {
            if (!$checkoutCart->getCrosssellBlock()->verifyProductCrosssell($relatedProduct)) {
                $errors[] = 'Product \'' . $relatedProduct->getName() . '\' is absent in cross-sell section.';
            }
        }

        // Logout
        $logoutCustomerOnFrontendStep = $objectManager->create(
            '\Magento\Customer\Test\TestStep\LogoutCustomerOnFrontendStep'
        );
        $logoutCustomerOnFrontendStep->run();

        \PHPUnit_Framework_Assert::assertEmpty($errors, implode(" ", $errors));
    }

    /**
     * Text success product is displayed in cross-sell section
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is displayed in cross-sell section.';
    }
}
