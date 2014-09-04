<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Client\Browser;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Cms\Test\Page\CmsIndex;

/**
 * Test Creation for ProductsInCartReportEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer
 * 2. Create product
 *
 * Steps:
 * 1. Login as customer in frontend
 * 2. Add product to cart
 * 3. Logout
 * 4. Add product to cart as unregistered customer
 * 5. Perform all assertions
 *
 * @group Reports_(MX)
 * @ZephyrId MAGETWO-27952
 */
class ProductsInCartReportEntity extends Injectable
{
    /**
     * Cms index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Customer login page
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * Product view page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Page CustomerAccountLogout
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Prepare data
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
     * @param CatalogProductView $catalogProductView
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountLogout $customerAccountLogout,
        CatalogProductView $catalogProductView
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customerAccountLogout = $customerAccountLogout;
        $this->catalogProductView = $catalogProductView;
    }

    /**
     * Creation products in cart report entity
     *
     * @param CustomerInjectable $customer
     * @param CatalogProductSimple $product
     * @param string $unregistered
     * @param Browser $browser
     * @return void
     */
    public function test(
        CustomerInjectable $customer,
        CatalogProductSimple $product,
        $unregistered,
        Browser $browser
    ) {
        // Preconditions
        $product->persist();

        //Steps
        $this->cmsIndex->open()->getLinksBlock()->openLink("Log In");
        $this->customerAccountLogin->getLoginBlock()->login($customer);
        $productUrl = $_ENV['app_frontend_url'] . $product->getUrlKey() . '.html';
        $browser->open($productUrl);
        $this->catalogProductView->getViewBlock()->addToCart($product);
        if ($unregistered) {
            $this->customerAccountLogout->open();
            $browser->open($productUrl);
            $this->catalogProductView->getViewBlock()->addToCart($product);
        }
    }

    /**
     * Log out after test
     *
     * @return void
     */
    public function tearDown()
    {
        $this->customerAccountLogout->open();
    }
}
