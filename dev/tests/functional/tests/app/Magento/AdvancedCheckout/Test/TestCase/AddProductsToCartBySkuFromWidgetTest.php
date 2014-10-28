<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Test\TestCase;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Widget\Test\Fixture\Widget;

/**
 * Test Flow:
 *
 * Preconditions:
 * 1. Register Customer
 * 2. Create Product
 * 3. Create widget "Order by Sku"
 *
 * Steps:
 * 1. Login to Frontend
 * 2. Navigate to My Account
 * 3. Fill data in widget according to dataSet
 * 4. Click Add to Cart button
 * 5. Perform all asserts
 *
 * @group Add_by_SKU_(CS)
 * @ZephyrId MAGETWO-29781
 */
class AddProductsToCartBySkuFromWidgetTest extends AbstractAdvancedCheckoutEntityTest
{
    /**
     * Order by SKU widget.
     *
     * @var Widget
     */
    protected static $widget;

    /**
     * Create customer and widget.
     *
     * @param CustomerInjectable $customer
     * @return array
     */
    public function __prepare(CustomerInjectable $customer)
    {
        $customer->persist();
        $fixtureFactory = $this->objectManager->create('\Mtf\Fixture\FixtureFactory');
        self::$widget = $fixtureFactory->createByCode('widget', ['dataSet' => 'order_by_sku']);
        self::$widget->persist();
        $adminCache = $this->objectManager->create('\Magento\Backend\Test\Page\Adminhtml\AdminCache');
        $adminCache->open();
        $adminCache->getActionsBlock()->flushMagentoCache();
        $adminCache->getMessagesBlock()->waitSuccessMessage();

        return ['customer' => $customer];
    }

    /**
     * Adding to cart AdvancedCheckoutEntity(from Widget).
     *
     * @param CustomerInjectable $customer
     * @param string $products
     * @param array $orderOptions
     * @param string $cartBlock
     * @return array
     */
    public function test(CustomerInjectable $customer, $products, array $orderOptions, $cartBlock)
    {
        // Preconditions
        $products = $this->createProducts($products);
        $orderOptions = $this->prepareOrderOptions($products, $orderOptions);
        // Steps
        $this->cmsIndex->open();
        $this->loginCustomer($customer);
        $this->cmsIndex->getLinksBlock()->openLink("My Account");
        $this->customerAccountIndex->getOrderBySkuBlock()->fillForm($orderOptions);
        $this->customerAccountIndex->getOrderBySkuBlock()->addToCart();

        $filteredProducts = $this->filterProducts($products, $cartBlock);

        return [
            'products' => isset($filteredProducts['cart']) ? $filteredProducts['cart'] : [],
            'requiredAttentionProducts' => isset($filteredProducts['required_attention'])
                ? $filteredProducts['required_attention']
                : []
        ];
    }

    /**
     * Clear shopping cart.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->checkoutCart->open()->getCartBlock()->clearShoppingCart();
    }

    /**
     * Delete widget.
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        self::$widgetInstanceIndex->open();
        self::$widgetInstanceIndex->getWidgetGrid()->searchAndOpen(['title' => self::$widget->getTitle()]);
        self::$widgetInstanceEdit->getPageActionsBlock()->delete();
    }
}
