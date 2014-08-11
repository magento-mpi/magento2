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
use Magento\Backend\Test\Page\AdminCache;
use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceEdit;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;
use Magento\MultipleWishlist\Test\Page\MultipleWishlistIndex;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlistWidget;

/**
 * Test Creation for CreateMultipleWishlistEntity
 *
 * Test Flow:
 * 1. Login to frontend as a Customer.
 * 2. Navigate to: My Account > My Wishlist.
 * 3. Start creating Wishlist.
 * 4. Fill in data according to attached data set.
 * 5. Perform appropriate assertions.
 *
 * @group Multiple_Wishlists_(CS)
 * @ZephyrId MAGETWO-27157
 */
class CreateMultipleWishlistEntityTest extends Injectable
{
    /**
     * Customer fixture
     *
     * @var CustomerInjectable
     */
    protected $customer;

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
     * Multiple wish list index page
     *
     * @var MultipleWishlistIndex
     */
    protected $multipleWishlistIndex;

    /**
     * Fixture factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Multiple wish list widget
     *
     * @var MultipleWishlistWidget
     */
    protected $wishlistSearch;

    /**
     * Widget instance edit page
     *
     * @var WidgetInstanceEdit
     */
    protected static $widgetInstanceEdit;

    /**
     * Wish list id
     *
     * @var array
     */
    protected static $wishlistId;

    /**
     * Browser object
     *
     * @var Browser
     */
    protected static $browser;

    /**
     * Prepare data
     *
     * @param FixtureFactory $fixtureFactory
     * @param CustomerInjectable $customer
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CatalogCategory $category
     * @param AdminCache $cache
     * @return array
     */
    public function __prepare(
        FixtureFactory $fixtureFactory,
        CustomerInjectable $customer,
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CatalogCategory $category,
        AdminCache $cache
    ) {
        $this->fixtureFactory = $fixtureFactory;
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->applyConfig('multiple_wishlist_default');
        $this->customer = $this->createCustomer($customer);
        $category->persist();
        $this->addWishlistSearch($cache);

        return ['category' => $category, 'customer' => $this->customer];
    }

    /**
     * Injection data
     *
     * @param CustomerAccountIndex $customerAccountIndex
     * @param MultipleWishlistIndex $multipleWishlistIndex
     * @param WidgetInstanceEdit $widgetInstanceEdit
     * @param Browser $browser
     * @return void
     */
    public function __inject(
        CustomerAccountIndex $customerAccountIndex,
        MultipleWishlistIndex $multipleWishlistIndex,
        WidgetInstanceEdit $widgetInstanceEdit,
        Browser $browser
    ) {
        $this->multipleWishlistIndex = $multipleWishlistIndex;
        $this->customerAccountIndex = $customerAccountIndex;
        self::$widgetInstanceEdit = $widgetInstanceEdit;
        self::$browser = $browser;
    }

    /**
     * Create new multiple wish list
     *
     * @param MultipleWishlist $multipleWishlist
     * @return void
     */
    public function test(MultipleWishlist $multipleWishlist)
    {
        //Steps
        $this->loginCustomer();
        $this->cmsIndex->open()->getLinksBlock()->openLink('My Account');
        $this->customerAccountIndex->getAccountMenuBlock()->openMenuItem('My Wish List');
        $this->multipleWishlistIndex->getManagementBlock()->clickCreateWishlist();
        $this->multipleWishlistIndex->getBehaviourBlock()->fill($multipleWishlist);
        $this->multipleWishlistIndex->getBehaviourBlock()->save();
    }

    /**
     * Create customer
     *
     * @param CustomerInjectable $customer
     * @return CustomerInjectable
     */
    protected function createCustomer(CustomerInjectable $customer)
    {
        $customer->persist();
        return $customer;
    }

    /**
     * Apply config data
     *
     * @param string $dataSet
     * @return void
     */
    protected function applyConfig($dataSet)
    {
        $config = $this->fixtureFactory->createByCode('configData', ['dataSet' => $dataSet]);
        $config->persist();
    }

    /**
     * Login customer
     *
     * @return void
     */
    protected function loginCustomer()
    {
        $this->cmsIndex->open();
        if (!$this->cmsIndex->getLinksBlock()->isLinkVisible('Log Out')) {
            $this->cmsIndex->getLinksBlock()->openLink("Log In");
            $this->customerAccountLogin->getLoginBlock()->login($this->customer);
        }
    }

    /**
     * Add wish list search widget
     *
     * @param AdminCache $cache
     * @return void
     */
    protected function addWishlistSearch(AdminCache $cache)
    {
        $wishlistSearch = $this->fixtureFactory->createByCode('multipleWishlistWidget', ['dataSet' => 'add_search']);
        $wishlistSearch->persist();
        self::$wishlistId = $wishlistSearch->getId();
        $this->wishlistSearch = $wishlistSearch;
        $cache->open();
        $cache->getActionsBlock()->flushMagentoCache();
        $cache->getMessagesBlock()->assertSuccessMessage();
    }

    /**
     * Inactive multiple wish list in config and delete wish list search widget
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        $fixtureFactory = ObjectManager::getInstance()->create('Mtf\Fixture\FixtureFactory');
        $config = $fixtureFactory->createByCode('configData', ['dataSet' => 'inactive_multiple_wishlist_default']);
        $config->persist();
        self::$browser->open(
            $_ENV['app_backend_url'] . 'admin/widget_instance/edit/instance_id/'
            . self::$wishlistId . '/code/wishlist_search/'
        );
        self::$widgetInstanceEdit->getPageAction()->deleteWishlist();
    }
}
