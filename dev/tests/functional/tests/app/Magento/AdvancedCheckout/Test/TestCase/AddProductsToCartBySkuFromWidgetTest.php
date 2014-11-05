<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Test\TestCase;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\AdvancedCheckout\Test\Fixture\Widget;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceIndex;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceEdit;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\AdvancedCheckout\Test\Page\CustomerOrderSku;
use Magento\Backend\Test\Page\Adminhtml\AdminCache;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Fixture\FixtureFactory;

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
     * Widget instance page.
     *
     * @var WidgetInstanceIndex
     */
    protected static $widgetInstanceIndex;

    /**
     * Widget instance edit page.
     *
     * @var WidgetInstanceEdit
     */
    protected static $widgetInstanceEdit;

    /**
     * Order by SKU widget.
     *
     * @var Widget
     */
    protected static $widget;

    /**
     * Injection data.
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountIndex $customerAccountIndex
     * @param CustomerOrderSku $customerOrderSku
     * @param CheckoutCart $checkoutCart
     * @param WidgetInstanceIndex $widgetInstanceIndex
     * @param WidgetInstanceEdit $widgetInstanceEdit
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountIndex $customerAccountIndex,
        CustomerOrderSku $customerOrderSku,
        CheckoutCart $checkoutCart,
        WidgetInstanceIndex $widgetInstanceIndex,
        WidgetInstanceEdit $widgetInstanceEdit
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountIndex = $customerAccountIndex;
        $this->customerOrderSku = $customerOrderSku;
        $this->checkoutCart = $checkoutCart;
        self::$widgetInstanceIndex = $widgetInstanceIndex;
        self::$widgetInstanceEdit = $widgetInstanceEdit;
    }

    /**
     * Create customer and widget.
     *
     * @param CustomerInjectable $customer
     * @param FixtureFactory $fixtureFactory
     * @param AdminCache $adminCache
     * @return array
     */
    public function __prepare(CustomerInjectable $customer, FixtureFactory $fixtureFactory, AdminCache $adminCache)
    {
        $customer->persist();
        self::$widget = $fixtureFactory->create(
            '\Magento\AdvancedCheckout\Test\Fixture\Widget',
            ['dataSet' => 'order_by_sku']
        );
        self::$widget->persist();
        $adminCache->open();
        $adminCache->getActionsBlock()->flushMagentoCache();
        $adminCache->getMessagesBlock()->waitSuccessMessage();

        return ['customer' => $customer];
    }

    /**
     * Add product to cart by sku from widget.
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
