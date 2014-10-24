<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Test\TestCase;

use Magento\Customer\Test\Fixture\CustomerInjectable;

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
class AddToCartAdvancedCheckoutEntityTest extends AbstractAdvancedCheckoutEntityTest
{
    /**
     * Adding to cart AdvancedCheckoutEntity(from MyAccount)
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
