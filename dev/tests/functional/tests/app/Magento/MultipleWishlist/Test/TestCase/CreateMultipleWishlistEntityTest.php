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
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceEdit;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;
use Magento\MultipleWishlist\Test\Page\MultipleWishlistIndex;

/**
 * Test Creation for CreateMultipleWishlistEntity
 *
 * Preconditions:
 * 1. Enable Multiple Wishlist functionality & set "Number of Multiple Wish Lists = 3.
 * 2. Create Customer Account.
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
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreateMultipleWishlistEntityTest extends Injectable
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
     * Multiple wish list index page
     *
     * @var MultipleWishlistIndex
     */
    protected $multipleWishlistIndex;

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
     * @param CatalogCategory $category
     * @param AdminCache $cachePage
     * @return array
     */
    public function __prepare(
        FixtureFactory $fixtureFactory,
        CustomerInjectable $customer,
        CatalogCategory $category,
        AdminCache $cachePage
    ) {
        $this->fixtureFactory = $fixtureFactory;
        $config = $fixtureFactory->createByCode('configData', ['dataSet' => 'multiple_wishlist_default']);
        $config->persist();
        $customer->persist();
        $category->persist();
        $this->createWishlistSearchWidget($cachePage);

        return ['category' => $category, 'customer' => $customer];
    }

    /**
     * Injection data
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountIndex $customerAccountIndex
     * @param MultipleWishlistIndex $multipleWishlistIndex
     * @param WidgetInstanceEdit $widgetInstanceEdit
     * @param Browser $browser
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountIndex $customerAccountIndex,
        MultipleWishlistIndex $multipleWishlistIndex,
        WidgetInstanceEdit $widgetInstanceEdit,
        Browser $browser
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->multipleWishlistIndex = $multipleWishlistIndex;
        $this->customerAccountIndex = $customerAccountIndex;
        self::$widgetInstanceEdit = $widgetInstanceEdit;
        self::$browser = $browser;
    }

    /**
     * Create new multiple wish list
     *
     * @param MultipleWishlist $multipleWishlist
     * @param CustomerInjectable $customer
     * @return void
     */
    public function test(MultipleWishlist $multipleWishlist, CustomerInjectable $customer)
    {
        //Steps
        $this->loginCustomer($customer);
        $this->cmsIndex->open()->getLinksBlock()->openLink('My Account');
        $this->customerAccountIndex->getAccountMenuBlock()->openMenuItem('My Wish List');
        $this->multipleWishlistIndex->getManagementBlock()->clickCreateNewWishlist();
        $this->multipleWishlistIndex->getBehaviourBlock()->fill($multipleWishlist);
        $this->multipleWishlistIndex->getBehaviourBlock()->save();
    }

    /**
     * Login customer
     *
     * @param CustomerInjectable $customer
     * @return void
     */
    protected function loginCustomer(CustomerInjectable $customer)
    {
        $this->cmsIndex->open();
        if (!$this->cmsIndex->getLinksBlock()->isLinkVisible('Log Out')) {
            $this->cmsIndex->getLinksBlock()->openLink("Log In");
            $this->customerAccountLogin->getLoginBlock()->login($customer);
        }
    }

    /**
     * Add wish list search widget
     *
     * @param AdminCache $cache
     * @return void
     */
    protected function createWishlistSearchWidget(AdminCache $cache)
    {
        $wishlistSearch = $this->fixtureFactory->create(
            'Magento\MultipleWishlist\Test\Fixture\Widget',
            ['dataSet' => 'add_search']
        );
        $wishlistSearch->persist();
        self::$wishlistId = $wishlistSearch->getId();
        $cache->open();
        $cache->getActionsBlock()->flushMagentoCache();
    }

    /**
     * Inactive multiple wish list in config and delete wish list search widget
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        $config = ObjectManager::getInstance()->create(
            'Magento\Core\Test\Fixture\ConfigData',
            ['dataSet' => 'disabled_multiple_wishlist_default']
        );
        $config->persist();
        self::$browser->open(
            $_ENV['app_backend_url'] . 'admin/widget_instance/edit/instance_id/'
            . self::$wishlistId . '/code/wishlist_search/'
        );
        self::$widgetInstanceEdit->getPageActionsBlock()->delete();
    }
}
