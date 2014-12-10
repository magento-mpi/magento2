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
    protected $widgetInstanceIndex;

    /**
     * Widget instance edit page.
     *
     * @var WidgetInstanceEdit
     */
    protected $widgetInstanceEdit;

    /**
     * Order by SKU widget.
     *
     * @var Widget
     */
    protected $widget;

    /**
     * Fixture Factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Page AdminCache.
     *
     * @var AdminCache
     */
    protected $adminCache;

    /**
     * Injection data.
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountIndex $customerAccountIndex
     * @param CustomerOrderSku $customerOrderSku
     * @param CheckoutCart $checkoutCart
     * @param WidgetInstanceIndex $widgetInstanceIndex
     * @param WidgetInstanceEdit $widgetInstanceEdit
     * @param FixtureFactory $fixtureFactory
     * @param AdminCache $adminCache
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountIndex $customerAccountIndex,
        CustomerOrderSku $customerOrderSku,
        CheckoutCart $checkoutCart,
        WidgetInstanceIndex $widgetInstanceIndex,
        WidgetInstanceEdit $widgetInstanceEdit,
        FixtureFactory $fixtureFactory,
        AdminCache $adminCache
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountIndex = $customerAccountIndex;
        $this->customerOrderSku = $customerOrderSku;
        $this->checkoutCart = $checkoutCart;
        $this->widgetInstanceIndex = $widgetInstanceIndex;
        $this->widgetInstanceEdit = $widgetInstanceEdit;
        $this->fixtureFactory = $fixtureFactory;
        $this->adminCache = $adminCache;
    }

    /**
     * Create customer and widget.
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
        $this->widget = $this->fixtureFactory->create(
            '\Magento\AdvancedCheckout\Test\Fixture\Widget',
            ['dataSet' => 'order_by_sku']
        );
        $this->widget->persist();
        $this->adminCache->open();
        $this->adminCache->getActionsBlock()->flushMagentoCache();
        $this->adminCache->getMessagesBlock()->waitSuccessMessage();
        // Steps
        $this->cmsIndex->open();
        $this->loginCustomer($customer);
        $this->cmsIndex->getLinksBlock()->openLink("My Account");
        $this->customerAccountIndex->getAccountMenuBlock()->openMenuItem("Order by SKU");
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
     * Clear shopping cart and delete widget.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->checkoutCart->open()->getCartBlock()->clearShoppingCart();
        $this->widgetInstanceIndex->open();
        $this->widgetInstanceIndex->getWidgetGrid()->searchAndOpen(['title' => $this->widget->getTitle()]);
        $this->widgetInstanceEdit->getPageActionsBlock()->delete();
    }
}
