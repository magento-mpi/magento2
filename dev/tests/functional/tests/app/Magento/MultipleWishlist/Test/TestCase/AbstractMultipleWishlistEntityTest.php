<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\TestCase;

use Mtf\ObjectManager;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Client\Driver\Selenium\Browser;
use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Backend\Test\Page\Adminhtml\AdminCache;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceEdit;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;
use Magento\Wishlist\Test\Page\WishlistIndex;

/**
 * Abstract Class AbstractMultipleWishlistEntityTest
 * Abstract Class for multiple wish list entity tests.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractMultipleWishlistEntityTest extends Injectable
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
     * Customer account index page
     *
     * @var CustomerAccountIndex
     */
    protected $customerAccountIndex;

    /**
     * Multiple wish list index page
     *
     * @var WishlistIndex
     */
    protected $wishlistIndex;

    /**
     * Catalog product view page.
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Admin cache page
     *
     * @var AdminCache
     */
    protected static $cachePage;

    /**
     * Widget instance edit page
     *
     * @var WidgetInstanceEdit
     */
    protected static $widgetInstanceEdit;

    /**
     * Browser object
     *
     * @var Browser
     */
    protected $browser;

    /**
     * Prepare data.
     *
     * @param FixtureFactory $fixtureFactory
     * @param CustomerInjectable $customer
     * @param CatalogCategory $category
     * @param AdminCache $cachePage
     * @param WidgetInstanceEdit $widgetInstanceEdit
     * @param Browser $browser
     * @return array
     */
    public function __prepare(
        FixtureFactory $fixtureFactory,
        CustomerInjectable $customer,
        CatalogCategory $category,
        AdminCache $cachePage,
        WidgetInstanceEdit $widgetInstanceEdit,
        Browser $browser
    ) {
        self::$widgetInstanceEdit = $widgetInstanceEdit;
        self::$cachePage = $cachePage;
        $this->browser = $browser;
        $this->fixtureFactory = $fixtureFactory;
        $this->setupConfiguration('multiple_wishlist_default');
        $customer->persist();
        $category->persist();

        return ['category' => $category, 'customer' => $customer];
    }

    /**
     * Injection data.
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountIndex $customerAccountIndex
     * @param WishlistIndex $wishlistIndex
     * @param CatalogProductView $catalogProductView
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountIndex $customerAccountIndex,
        WishlistIndex $wishlistIndex,
        CatalogProductView $catalogProductView
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->wishlistIndex = $wishlistIndex;
        $this->customerAccountIndex = $customerAccountIndex;
        $this->catalogProductView = $catalogProductView;
    }

    /**
     * Create multiple wish list.
     *
     * @param MultipleWishlist $multipleWishlist
     * @param CustomerInjectable $customer
     * @return MultipleWishlist
     */
    protected function createMultipleWishlist(MultipleWishlist $multipleWishlist, CustomerInjectable $customer)
    {
        $data = $multipleWishlist->getData();
        $data['customer_id'] = ['customer' => $customer];
        $multipleWishlist = $this->fixtureFactory->createByCode('multipleWishlist', ['data' => $data]);
        $multipleWishlist->persist();

        return $multipleWishlist;
    }

    /**
     * Add wish list search widget.
     *
     * @return void
     */
    protected function createWishlistSearchWidget()
    {
        $wishlistSearch = $this->fixtureFactory->create(
            'Magento\MultipleWishlist\Test\Fixture\Widget',
            ['dataSet' => 'add_search']
        );
        $wishlistSearch->persist();
        self::$cachePage->open()->getActionsBlock()->flushMagentoCache();
    }

    /**
     * Log in customer on frontend.
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
     * Open wish list page.
     *
     * @param CustomerInjectable $customer
     * @return void
     */
    protected function openWishlistPage(CustomerInjectable $customer)
    {
        $this->loginCustomer($customer);
        $this->cmsIndex->getLinksBlock()->openLink('My Account');
        $this->customerAccountIndex->getAccountMenuBlock()->openMenuItem('My Wish List');
    }

    /**
     * Setup configuration.
     *
     * @param string $configData
     * @param bool $rollback
     * @return void
     */
    public static function setupConfiguration($configData, $rollback = false)
    {
        $setConfigStep = ObjectManager::getInstance()->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => $configData, 'rollback' => $rollback]
        );
        $setConfigStep->run();
    }

    /**
     * Disable multiple wish list in config.
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        self::setupConfiguration('multiple_wishlist_default', true);
    }
}
