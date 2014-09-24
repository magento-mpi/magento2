<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\TestCase;

use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Mtf\Client\Driver\Selenium\Browser;
use Mtf\Fixture\FixtureFactory;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;
use Magento\Wishlist\Test\Page\WishlistIndex;
use Mtf\ObjectManager;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\InjectableFixture;


/**
 * Test Creation for MoveToAnotherWishlist
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
     * Customer account index page
     *
     * @var CustomerAccountIndex
     */
    protected $customerAccountIndex;

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
     * Customer Account Logout page
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

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
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountIndex $customerAccountIndex
     * @param WishlistIndex $wishlistIndex
     * @param CatalogProductView $catalogProductView
     * @param CustomerAccountLogout $customerAccountLogout
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountIndex $customerAccountIndex,
        WishlistIndex $wishlistIndex,
        CatalogProductView $catalogProductView,
        CustomerAccountLogout $customerAccountLogout
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->wishlistIndex = $wishlistIndex;
        $this->customerAccountIndex = $customerAccountIndex;
        $this->catalogProductView = $catalogProductView;
        $this->customerAccountLogout = $customerAccountLogout;
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
            [
                'dataSet' => $dataSet,
                'data' => [
                    'checkout_data' => $fixture == 'groupedProductInjectable'
                            ? []
                            : ['qty' => $qty]
                ]
            ]
        );
        $product->persist();
        return $product;
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
        $this->catalogProductView->getMultipleWishlistViewBlock()->fillOptionsAndaddToWishlist($product);
    }
}
