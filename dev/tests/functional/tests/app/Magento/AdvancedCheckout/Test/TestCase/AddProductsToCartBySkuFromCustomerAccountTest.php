<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\AdvancedCheckout\Test\TestCase;

use Magento\AdvancedCheckout\Test\Page\CustomerOrderSku;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountIndex;

/**
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
class AddProductsToCartBySkuFromCustomerAccountTest extends AbstractAdvancedCheckoutEntityTest
{
    /**
     * Injection data.
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountIndex $customerAccountIndex
     * @param CustomerOrderSku $customerOrderSku
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountIndex $customerAccountIndex,
        CustomerOrderSku $customerOrderSku,
        CheckoutCart $checkoutCart
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountIndex = $customerAccountIndex;
        $this->customerOrderSku = $customerOrderSku;
        $this->checkoutCart = $checkoutCart;
    }

    /**
     * Create customer.
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
     * Adding to cart AdvancedCheckoutEntity(from MyAccount).
     *
     * @param CustomerInjectable $customer
     * @param string $products
     * @param array $orderOptions
     * @param string $cartBlock
     * @param string $config
     * @return array
     */
    public function test(CustomerInjectable $customer, $products, array $orderOptions, $cartBlock, $config)
    {
        // Preconditions
        $this->configuration = $config;
        $this->setupConfiguration();
        $products = $this->createProducts($products);
        $orderOptions = $this->prepareOrderOptions($products, $orderOptions);
        // Steps
        $this->cmsIndex->open();
        $this->loginCustomer($customer);
        $this->cmsIndex->getLinksBlock()->openLink("My Account");
        $this->customerAccountIndex->getAccountMenuBlock()->openMenuItem("Order by SKU");
        $this->customerOrderSku->getCustomerSkuBlock()->fillForm($orderOptions);
        $this->customerOrderSku->getCustomerSkuBlock()->addToCart();

        $filteredProducts = $this->filterProducts($products, $cartBlock);

        return [
            'products' => isset($filteredProducts['cart']) ? $filteredProducts['cart'] : [],
            'requiredAttentionProducts' => isset($filteredProducts['required_attention'])
                ? $filteredProducts['required_attention']
                : []
        ];
    }

    /**
     * Clear shopping cart and set configuration after test.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->checkoutCart->open()->getCartBlock()->clearShoppingCart();
        $this->setupConfiguration(true);
    }
}
