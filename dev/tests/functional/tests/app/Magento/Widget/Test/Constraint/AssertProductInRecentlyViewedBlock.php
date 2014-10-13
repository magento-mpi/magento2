<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Constraint;

use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\FixtureFactory;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Backend\Test\Page\Adminhtml\AdminCache;
use Mtf\Fixture\InjectableFixture;
use Magento\Widget\Test\Fixture\Widget;
use Mtf\Client\Browser;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Sales\Test\Page\Adminhtml\OrderCreateIndex;

/**
 * Class AssertProductInRecentlyViewedBlock
 * Check that that widget with type Recently Viewed Products is present on category and order page
 */
class AssertProductInRecentlyViewedBlock extends AbstractConstraint
{
    /**
     * Browser
     *
     * @var Browser
     */
    protected $browser;

    /**
     * Catalog product page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Cms index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Category Page on Frontend
     *
     * @var CatalogCategoryView
     */
    protected $catalogCategoryView;

    /**
     * Order create index page
     *
     * @var OrderCreateIndex
     */
    protected $orderCreateIndex;

    /**
     * Factory for fixture
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that widget with type Recently Viewed Products is present on category and order page
     *
     * @param FixtureFactory $fixtureFactory
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param AdminCache $adminCache
     * @param Widget $widget
     * @param CatalogProductView $catalogProductView
     * @param CatalogCategoryView $catalogCategoryView
     * @param Browser $browser
     * @param CustomerIndex $customerIndexPage
     * @param OrderCreateIndex $orderCreateIndex
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        FixtureFactory $fixtureFactory,
        CustomerAccountLogin $customerAccountLogin,
        AdminCache $adminCache,
        Widget $widget,
        CatalogProductView $catalogProductView,
        CatalogCategoryView $catalogCategoryView,
        Browser $browser,
        CustomerIndex $customerIndexPage,
        OrderCreateIndex $orderCreateIndex
    ) {
        // Flush cache
        $adminCache->open();
        $adminCache->getActionsBlock()->flushMagentoCache();
        $adminCache->getMessagesBlock()->waitSuccessMessage();

        $this->catalogProductView = $catalogProductView;
        $this->browser = $browser;
        $this->fixtureFactory = $fixtureFactory;
        $this->cmsIndex = $cmsIndex;
        $this->catalogCategoryView = $catalogCategoryView;
        $this->orderCreateIndex = $orderCreateIndex;
        $products = [];

        $customer = $this->fixtureFactory->createByCode('customerInjectable', ['dataSet' => 'default']);
        $customer->persist();

        $this->cmsIndex->open();
        $this->cmsIndex->getLinksBlock()->openLink('Log In');
        $customerAccountLogin->getLoginBlock()->login($customer);

        if (!($widget->getWidgetOptions()[0]['entities'] instanceof InjectableFixture)) {
            foreach ($widget->getWidgetOptions()[0]['entities'] as $product) {
                $products[] = $product;
            }
        } else {
            $products[] = $widget->getWidgetOptions()[0]['entities'];
        }
        $this->openProducts($products);
        $this->checkRecentlyViewedBlockOnCategory($widget);

        $filter = ['email' => $customer->getEmail()];
        $customerIndexPage->open();
        $customerIndexPage->getCustomerGridBlock()->searchAndOpen($filter);

        $createOrderFromCustomer = $this->objectManager
            ->create('Magento\Customer\Test\TestStep\CreateOrderFromCustomerAccountStep');
        $createOrderFromCustomer->run();

        $this->checkRecentlyViewedBlockOnOrder($widget);
    }

    /**
     * Open products
     *
     * @param array $products
     * @return void
     */
    protected function openProducts(array $products)
    {
        foreach ($products as $itemProduct) {
            $this->browser->open($_ENV['app_frontend_url'] . $itemProduct->getUrlKey() . '.html');
        }
    }

    /**
     * Check that block Recently Viewed contains product on category page
     *
     * @param Widget $widget
     * @return void
     */
    protected function checkRecentlyViewedBlockOnCategory(Widget $widget)
    {
        $category = $this->fixtureFactory->createByCode('catalogCategory', ['dataSet' => 'default_subcategory']);
        $category->persist();
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenu()->selectCategoryByName($category->getName());
        sleep(3);

        \PHPUnit_Framework_Assert::assertTrue(
            $this->catalogCategoryView->getViewBlock()->checkProductInRecentlyViewedBlock($widget),
            'Product is absent on Recently Viewed block on Category page.'
        );
    }

    /**
     * Check that block Recently Viewed contains product on order page
     *
     * @param Widget $widget
     * @return void
     */
    protected function checkRecentlyViewedBlockOnOrder(Widget $widget)
    {
        \PHPUnit_Framework_Assert::assertTrue(
            $this->orderCreateIndex->getCustomerActivitiesBlock()->getRecentlyViewedProductsBlock()
                ->checkProductInRecentlyViewedBlock($widget),
            'Product is absent on Recently Viewed block on order page.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "Widget with type Recently Viewed Products is present in all pages.";
    }
}
