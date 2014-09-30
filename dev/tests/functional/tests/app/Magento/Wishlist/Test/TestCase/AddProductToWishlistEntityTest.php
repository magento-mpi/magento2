<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Wishlist\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Client\Driver\Selenium\Browser;
use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\InjectableFixture;
use Mtf\ObjectManager;
use Magento\Wishlist\Test\Page\WishlistIndex;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Test Creation for AddProductToWishlistEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Customer is registered
 * 2. Product is created
 *
 * Steps:
 * 1. Login as a customer
 * 2. Navigate to catalog page
 * 3. Add created product to Wishlist according to dataSet
 * 4. Perform all assertions
 *
 * @group Wishlist_(CS)
 * @ZephyrId MAGETWO-29045
 */
class AddProductToWishlistEntityTest extends Injectable
{
    /**
     * Fixture factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Catalog product view page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Multiple wish list index page
     *
     * @var WishlistIndex
     */
    protected $wishlistIndex;

    /**
     * Browser object
     *
     * @var Browser
     */
    protected $browser;

    /**
     * ObjectManager object
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Prepare data for test
     *
     * @param FixtureFactory $fixtureFactory
     * @param Browser $browser
     * @param ObjectManager $objectManager
     * @return void
     */
    public function __prepare(FixtureFactory $fixtureFactory, Browser $browser, ObjectManager $objectManager)
    {
        $this->browser = $browser;
        $this->objectManager = $objectManager;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Injection data
     *
     * @param WishlistIndex $wishlistIndex
     * @param CatalogProductView $catalogProductView
     * @return void
     */
    public function __inject(
        WishlistIndex $wishlistIndex,
        CatalogProductView $catalogProductView
    ) {
        $this->wishlistIndex = $wishlistIndex;
        $this->catalogProductView = $catalogProductView;
    }

    /**
     * Run Add Product To Wishlist test
     *
     * @param CustomerInjectable $customer
     * @param string $product
     * @return array
     */
    public function test(CustomerInjectable $customer, $product)
    {
        $this->markTestIncomplete('Bug: MAGETWO-27949');
        // Preconditions:
        $customer->persist();
        $product = $this->createProduct($product);

        // Steps:
        $this->loginCustomer($customer);
        $this->addProductToWishlist($product);

        return ['product' => $product];
    }

    /**
     * Create product
     *
     * @param string $product
     * @return InjectableFixture
     */
    protected function createProduct($product)
    {
        list($fixture, $dataSet) = explode('::', $product);
        $product = $this->fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
        $product->persist();
        return $product;
    }

    /**
     * Login customer
     *
     * @param CustomerInjectable $customer
     * @return void
     */
    protected function loginCustomer(CustomerInjectable $customer)
    {
        $customerLogin = $this->objectManager->create(
            'Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        );
        $customerLogin->run();
    }

    /**
     * Add product to wishlist
     *
     * @param InjectableFixture $product
     * @return void
     */
    protected function addProductToWishlist($product)
    {
        $this->browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $viewBlock = $this->catalogProductView->getViewBlock();
        $viewBlock->fillOptions($product);
        $viewBlock->addToWishlist();
    }
}
