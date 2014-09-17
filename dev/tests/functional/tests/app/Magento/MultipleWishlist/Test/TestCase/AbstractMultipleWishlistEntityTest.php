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
 * Abstract Class AbstractMultipleWishlistEntityTest
 * Abstract Class for multiple wish list entity tests
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
     * Wish list id
     *
     * @var array
     */
    protected static $wishlistId;

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
    protected static $browser;

    /**
     * Prepare data
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
        self::$browser = $browser;
        self::$cachePage = $cachePage;
        $this->fixtureFactory = $fixtureFactory;
        $config = $fixtureFactory->createByCode('configData', ['dataSet' => 'multiple_wishlist_default']);
        $config->persist();
        $customer->persist();
        $category->persist();

        return ['category' => $category, 'customer' => $customer];
    }

    /**
     * Injection data
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountIndex $customerAccountIndex
     * @param MultipleWishlistIndex $multipleWishlistIndex
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountIndex $customerAccountIndex,
        MultipleWishlistIndex $multipleWishlistIndex
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->multipleWishlistIndex = $multipleWishlistIndex;
        $this->customerAccountIndex = $customerAccountIndex;
    }

    /**
     * Create multiple wish list
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
     * Add wish list search widget
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
        self::$wishlistId = $wishlistSearch->getId();
        self::$cachePage->open()->getActionsBlock()->flushMagentoCache();
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
        $linksBlock = $this->cmsIndex->getLinksBlock();
        if (!$linksBlock->isLinkVisible('Log Out')) {
            $linksBlock->openLink("Log In");
            $this->customerAccountLogin->getLoginBlock()->login($customer);
        }
    }

    /**
     * Open wish list page
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
}
