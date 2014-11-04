<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\TestCase;

use Mtf\ObjectManager;
use Mtf\Fixture\FixtureFactory;
use Mtf\Client\Driver\Selenium\Browser;
use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Backend\Test\Page\Adminhtml\AdminCache;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceEdit;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;

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
        $result = parent::__prepare(
            $fixtureFactory,
            $customer,
            $category,
            $cachePage,
            $widgetInstanceEdit,
            $browser
        );
        $this->createWishlistSearchWidget();

        return $result;
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
        $this->markTestIncomplete("Bug: MAGETWO-30155");

        //Steps
        $this->openWishlistPage($customer);
        $this->wishlistIndex->getManagementBlock()->clickCreateNewWishlist();
        $this->wishlistIndex->getBehaviourBlock()->fill($multipleWishlist);
        $this->wishlistIndex->getBehaviourBlock()->save();
    }

    /**
     * Inactive multiple wish list in config and delete wish list search widget
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        self::$browser->open(
            $_ENV['app_backend_url'] . 'admin/widget_instance/edit/instance_id/'
            . self::$wishlistId . '/code/wishlist_search/'
        );
        self::$widgetInstanceEdit->getPageActionsBlock()->delete();
        self::$cachePage->open()->getActionsBlock()->flushMagentoCache();
    }
}
