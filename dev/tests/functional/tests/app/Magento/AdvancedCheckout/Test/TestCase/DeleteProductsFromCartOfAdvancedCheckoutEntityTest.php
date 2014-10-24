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
     * Delete requiring attention products
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
