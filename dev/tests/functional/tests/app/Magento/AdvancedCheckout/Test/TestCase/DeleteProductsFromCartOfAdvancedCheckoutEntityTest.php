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
 * 1. Product is created according to dataSet
 * 2. Clear shopping cart
 * 3. Create Customer
 * 4. Add to cart product by SKU
 *
 * Steps:
 * 1. Login to Frontend
 * 2. Open Shopping Cart
 * 3. Click Remove button in Product Requiring Section
 * 4. Perform all asserts
 *
 * @group Add_by_SKU_(CS)
 * @ZephyrId MAGETWO-29906
 */
class DeleteProductsFromCartOfAdvancedCheckoutEntityTest extends AbstractAdvancedCheckoutEntityTest
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
     * Delete products from AdvancedCheckout.
     *
     * @param CustomerInjectable $customer
     * @param string $products
     * @param string $orderOptions
     * @return array
     */
    public function test(CustomerInjectable $customer, $products, $orderOptions)
    {
        // Preconditions
        $products = $this->createProducts($products);
        $orderOptions = $this->prepareOrderOptions($products, $orderOptions);

        $this->checkoutCart->open()->getCartBlock()->clearShoppingCart();

        $this->cmsIndex->open();
        $this->loginCustomer($customer);
        $this->cmsIndex->getLinksBlock()->openLink("My Account");
        $this->customerAccountIndex->getAccountMenuBlock()->openMenuItem("Order by SKU");
        $this->customerOrderSku->getCustomerSkuBlock()->fillForm($orderOptions);
        $this->customerOrderSku->getCustomerSkuBlock()->addToCart();

        // Steps
        $this->checkoutCart->open();
        $this->deleteProducts($products);

        return ['products' => $products];
    }

    /**
     * Delete requiring attention products.
     *
     * @param array $products
     * @return void
     */
    protected function deleteProducts(array $products)
    {
        foreach ($products as $product) {
            $this->checkoutCart->getAdvancedCheckoutCart()->deleteProduct($product);
        }
    }
}
