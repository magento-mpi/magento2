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
     * @param string $products
     * @param array $orderOptions
     * @param array $cartBlock
     * @param string $config
     * @return array
     */
    public function test(CustomerInjectable $customer, $products, array $orderOptions, array $cartBlock, $config)
    {
        // Preconditions
        $this->configuration = $config;
        if ($this->configuration !== '-') {
            $this->setupConfiguration();
        }
        $products = $this->createProducts($products);
        $orderOptions = $this->prepareOrderOptions($products, $orderOptions);
        // Steps
        $this->cmsIndex->open();
        $this->cmsIndex->getLinksBlock()->openLink("Log In");
        $this->customerAccountLogin->getLoginBlock()->login($customer);
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
     * @param array $cartBlock
     * @return array
     */
    protected function filterProducts(array $products, array $cartBlock)
    {
        $filteredProducts = [];
        foreach ($cartBlock as $key => $value) {
            if ($value !== '-') {
                $filteredProducts[$value][$key] = $products[$key];
            }
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
        $products = explode(',', $products);
        foreach ($products as $key => $product) {
            list($fixture, $dataSet) = explode('::', trim($product));
            $product = $this->fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
            $product->persist();
            $products[$key] = $product;
        }
        return $products;
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
        foreach ($products as $key => $product) {
            $productSku = $product->getSku();
            switch ($orderOptions[$key]['sku']) {
                case "confCompoundSku":
                    $orderOptions[$key]['sku'] = $productSku . '-'
                        . $product->getConfigurableAttributesData()['matrix']['attribute_key_0:option_key_0']['sku'];
                    break;
                case "bundleCompoundSku":
                    $orderOptions[$key]['sku'] = $productSku . '-'
                        . $product->getBundleSelections()['products'][0][0]->getSku();
                    break;
                case "simpleWithOptionCompoundSku":
                    $orderOptions[$key]['sku'] = $productSku . '-'
                        . $product->getCustomOptions()[0]['options'][0]['sku'];
                    break;
                case "nonexistentSku":
                    $orderOptions[$key]['sku'] = 'nonexistentSku';
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
        $prefix = ($rollback == false) ? '' : '_rollback';
        $dataSets = explode(',', $this->configuration);
        foreach ($dataSets as $dataSet) {
            $dataSet = trim($dataSet) . $prefix;
            $configuration = $this->fixtureFactory->createByCode('configData', ['dataSet' => $dataSet]);
            $configuration->persist();
        }
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
        if ($this->configuration !== '-') {
            $this->setupConfiguration(true);
        }
    }
}
