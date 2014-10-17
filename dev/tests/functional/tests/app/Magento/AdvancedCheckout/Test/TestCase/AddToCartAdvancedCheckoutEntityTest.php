<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountIndex;
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
     * Customer account index page
     *
     * @var CustomerAccountIndex
     */
    protected $customerAccountIndex;

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
     * Configuration data set name
     *
     * @var string
     */
    protected $configuration;

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
     * Filter products
     *
     * @param array $products
     * @param string $cartBlock
     * @return array
     */
    protected function filterProducts(array $products, $cartBlock)
    {
        $filteredProducts = [];
        $cartBlock = explode(',', $cartBlock);
        foreach ($cartBlock as $key => $value) {
            $filteredProducts[trim($value)][$key] = $products[$key];
        }

        return $filteredProducts;
    }

    /**
     * Create products
     *
     * @param string $products
     * @return array
     */
    protected function createProducts($products)
    {
        $createProductsStep = $this->objectManager->create(
            'Magento\Catalog\Test\TestStep\CreateProductsStep',
            ['products' => $products]
        );

        return $createProductsStep->run()['products'];
    }

    /**
     * Prepare order options
     *
     * @param array $products
     * @param array $orderOptions
     * @return array
     */
    protected function prepareOrderOptions(array $products, array $orderOptions)
    {
        foreach ($orderOptions as $key => $value) {
            $options = explode(',', $value);
            foreach ($options as $item => $option) {
                $orderOptions[$item][$key] = trim($option);
            }
            unset($orderOptions[$key]);
        }

        foreach ($products as $key => $product) {
            $productSku = $product->getSku();
            switch ($orderOptions[$key]['sku']) {
                case "simpleWithOptionCompoundSku":
                    $orderOptions[$key]['sku'] = $productSku . '-'
                        . $product->getCustomOptions()[0]['options'][0]['sku'];
                    break;
                case "nonExistentSku":
                    $orderOptions[$key]['sku'] = 'nonExistentSku';
                    break;
                default:
                    $orderOptions[$key]['sku'] = $productSku;
            }
        }

        return $orderOptions;
    }

    /**
     * Setup configuration
     *
     * @param bool $rollback
     * @return void
     */
    protected function setupConfiguration($rollback = false)
    {
        $setConfigStep = $this->objectManager->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => $this->configuration, 'rollback' => $rollback]
        );
        $setConfigStep->run();
    }

    /**
     * Login customer
     *
     * @param CustomerInjectable $customer
     * @return void
     */
    protected function loginCustomer(CustomerInjectable $customer)
    {
        $loginCustomerOnFrontendStep = $this->objectManager->create(
            'Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        );
        $loginCustomerOnFrontendStep->run();
    }

    /**
     * Clear shopping cart and set configuration after test
     *
     * @return void
     */
    public function tearDown()
    {
        $this->checkoutCart->open()->getCartBlock()->clearShoppingCart();
        $this->setupConfiguration(true);
    }
}
