<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\TestCase;

use Mtf\ObjectManager;
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
class CreateMultipleWishlistEntityTest extends AbstractMultipleWishlistEntityTest
{
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
     * Injection data
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountIndex $customerAccountIndex
     * @param MultipleWishlistIndex $multipleWishlistIndex
     * @param AdminCache $cachePage
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountIndex $customerAccountIndex,
        MultipleWishlistIndex $multipleWishlistIndex,
        AdminCache $cachePage
    ) {
        parent::__inject(
            $cmsIndex,
            $customerAccountLogin,
            $customerAccountIndex,
            $multipleWishlistIndex,
            $cachePage
        );
        $this->createWishlistSearchWidget();
    }

    /**
     * Create new multiple wish list
     *
     * @param MultipleWishlist $multipleWishlist
     * @param CustomerInjectable $customer
     * @param WidgetInstanceEdit $widgetInstanceEdit
     * @param Browser $browser
     * @return void
     */
    public function test(
        MultipleWishlist $multipleWishlist,
        CustomerInjectable $customer,
        WidgetInstanceEdit $widgetInstanceEdit,
        Browser $browser
    ) {
        //Steps
        self::$widgetInstanceEdit = $widgetInstanceEdit;
        self::$browser = $browser;
        $this->openWishlistPage($customer);
        $this->multipleWishlistIndex->getManagementBlock()->clickCreateNewWishlist();
        $this->multipleWishlistIndex->getBehaviourBlock()->fill($multipleWishlist);
        $this->multipleWishlistIndex->getBehaviourBlock()->save();
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
        self::$cachePage->open()->getActionsBlock()->flushMagentoCache();
    }
}
