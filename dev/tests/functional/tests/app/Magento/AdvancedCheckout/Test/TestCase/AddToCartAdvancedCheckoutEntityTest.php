<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\AdvancedCheckout\Test\Page\CustomerOrderSku;
use Magento\Checkout\Test\Page\CheckoutCart;

/**
 * Test Creation for AddingToCart AdvancedCheckoutEntity(from MyAccount)
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Register Customer
 * 2. Create Product
 *
 * Steps:
 * 1. Login to Frontend
 * 2. Open My Account > Order by SKU
 * 3. Fill data according dataSet
 * 4. Click Add to Cart button
 * 5. Perform all asserts
 *
 * @group Add_by_SKU_(CS)
 * @ZephyrId MAGETWO-28259
 */
class AddToCartAdvancedCheckoutEntityTest extends Injectable
{
    /**
     * Cms index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Customer account login page
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * Customer account logout page
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Customer account index page
     *
     * @var CustomerAccountIndex
     */
    protected $customerAccountIndex;

    /**
     * Fixture Factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Customer order by SKU page
     *
     * @var CustomerOrderSku
     */
    protected $customerOrderSku;

    /**
     * Checkout cart page
     *
     * @var CheckoutCart
     */
    protected $checkoutCart;

    /**
     * Create customer
     *
     * @param CustomerInjectable $customer
     * @return array
     */
    public function __prepare(CustomerInjectable $customer)
    {
        $customer->persist();

        return ['customer' => $customer];
    }

    /**
     * Injection data
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountLogout $customerAccountLogout
     * @param CustomerAccountIndex $customerAccountIndex
     * @param FixtureFactory $fixtureFactory
     * @param CustomerOrderSku $customerOrderSku
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountLogout $customerAccountLogout,
        CustomerAccountIndex $customerAccountIndex,
        FixtureFactory $fixtureFactory,
        CustomerOrderSku $customerOrderSku,
        CheckoutCart $checkoutCart
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customerAccountLogout = $customerAccountLogout;
        $this->customerAccountIndex = $customerAccountIndex;
        $this->fixtureFactory = $fixtureFactory;
        $this->customerOrderSku = $customerOrderSku;
        $this->checkoutCart = $checkoutCart;
    }

    /**
     * Adding to cart AdvancedCheckoutEntity(from MyAccount)
     *
     * @param CustomerInjectable $customer
     * @param string $product
     * @param array $orderOptions
     * @return array
     */
    public function test(CustomerInjectable $customer, $product, array $orderOptions)
    {
        // Preconditions
        list($fixture, $dataSet) = explode('::', $product);
        $product = $this->fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
        $product->persist();
        $productSku = $product->getSku();
        $orderOptions['sku'] = ($orderOptions['sku'] === '%ConfSku%-%simpleSku%')
            ? $productSku . '-' . $product->getConfigurableAttributesData()['matrix']['attribute_0:option_0']['sku']
            : $productSku;
        // Steps
        $this->cmsIndex->open();
        $this->cmsIndex->getLinksBlock()->openLink("Log In");
        $this->customerAccountLogin->getLoginBlock()->login($customer);
        $this->cmsIndex->getLinksBlock()->openLink("My Account");
        $this->customerAccountIndex->getAccountMenuBlock()->openMenuItem("Order by SKU");
        $this->customerOrderSku->getCustomerSkuBlock()->fillForm($orderOptions);
        $this->customerOrderSku->getCustomerSkuBlock()->addToCart();

        return ['products' => [$product]];
    }

    /**
     * Clear shopping cart and log out after test
     *
     * @return void
     */
    public function tearDown()
    {
        $this->checkoutCart->open()->getCartBlock()->clearShoppingCart();
        $this->customerAccountLogout->open();
    }
}
