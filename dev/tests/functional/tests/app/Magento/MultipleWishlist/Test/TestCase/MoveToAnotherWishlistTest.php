<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\TestCase;

use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Mtf\Client\Driver\Selenium\Browser;
use Mtf\Fixture\FixtureFactory;
use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;
use Magento\Wishlist\Test\Page\WishlistIndex;
use Mtf\ObjectManager;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\InjectableFixture;

/**
 * Test Creation for MoveToAnotherWishlistTest
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable Multiple wishlist in config
 * 2. Register new customer
 * 3. Create one custom Wishlist
 * 4. Add product with qty defined in dataset to default Wishlist
 *
 * Steps:
 * 1. Login to the Frontend as a customer
 * 2. Open default wishlist
 * 3. Set qtyToMove and Move it to custom wishlist
 * 4. Perform assertions
 *
 * @group Multiple_Wishlists_(CS)
 * @ZephyrId MAGETWO-28820
 */
class MoveToAnotherWishlistTest extends Injectable
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
        $config = $this->fixtureFactory->createByCode('configData', ['dataSet' => 'multiple_wishlist_default']);
        $config->persist();
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
     * Run Move To Another Wishlist test
     *
     * @param MultipleWishlist $multipleWishlist
     * @param string $product
     * @param string $qty
     * @param string $qtyToMove
     * @return array
     */
    public function test(MultipleWishlist $multipleWishlist, $product, $qty, $qtyToMove)
    {
        // Precondition:
        $multipleWishlist->persist();
        $customer = $multipleWishlist->getDataFieldConfig('customer_id')['source']->getCustomer();
        $product = $this->createProduct($product, $qty);
        $this->loginCustomer($customer);
        $this->addProductToWishlist($product);

        // Steps:
        $productBlock = $this->wishlistIndex->getMultipleItemsBlock()->getItemProduct($product);
        $productBlock->fillProduct(['qty' => $qtyToMove]);
        $productBlock->moveToWishlist($multipleWishlist);

        return ['product' => $product];
    }

    /**
     * Create product
     *
     * @param string $product
     * @param string $qty
     * @return InjectableFixture
     */
    protected function createProduct($product, $qty)
    {
        list($fixture, $dataSet) = explode('::', $product);
        $product = $this->fixtureFactory->createByCode(
            $fixture,
            ['dataSet' => $dataSet, 'data' => ['checkout_data' => ['data' => ['qty' => $qty]]]]
        );
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
        $wishlistViewBlock = $this->catalogProductView->getMultipleWishlistViewBlock();
        $wishlistViewBlock->addToWishlist($product);
    }
}
