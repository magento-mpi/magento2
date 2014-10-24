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
 * Abstract class for AdvancedCheckoutEntity tests.
 */
abstract class AbstractAdvancedCheckoutEntityTest extends Injectable
{
    /**
     * Cms index page.
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Customer account index page.
     *
     * @var CustomerAccountIndex
     */
    protected $customerAccountIndex;

    /**
     * Customer order by SKU page.
     *
     * @var CustomerOrderSku
     */
    protected $customerOrderSku;

    /**
     * Checkout cart page.
     *
     * @var CheckoutCart
     */
    protected $checkoutCart;

    /**
     * Configuration data set name.
     *
     * @var string
     */
    protected $configuration;

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
     * Filter products.
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
     * Create products.
     *
     * @param string $products
     * @return array
     */
    protected function createProducts($products)
    {
        if ($products === '-') {
            return [null];
        }
        $createProductsStep = $this->objectManager->create(
            'Magento\Catalog\Test\TestStep\CreateProductsStep',
            ['products' => $products]
        );

        return $createProductsStep->run()['products'];
    }

    /**
     * Prepare order options.
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
            $productSku = $product === null
                ? $productSku = $orderOptions[$key]['sku']
                : $productSku = $product->getSku();
            $orderOptions[$key]['sku'] = $orderOptions[$key]['sku'] === 'simpleWithOptionCompoundSku'
                ? $productSku . '-' . $product->getCustomOptions()[0]['options'][0]['sku']
                : $productSku;
        }

        return $orderOptions;
    }

    /**
     * Setup configuration.
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
     * Login customer.
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
}
