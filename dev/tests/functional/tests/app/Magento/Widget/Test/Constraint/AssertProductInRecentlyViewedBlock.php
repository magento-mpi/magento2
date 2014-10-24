<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Fixture\FixtureFactory;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Widget\Test\Fixture\Widget;
use Magento\Backend\Test\Page\Adminhtml\AdminCache;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderCreateIndex;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;

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
     * Customer index page
     *
     * @var CustomerIndex
     */
    protected $customerIndexPage;

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
     * @param CmsIndex $cmsIndex
     * @param FixtureFactory $fixtureFactory
     * @param AdminCache $adminCache
     * @param Widget $widget
     * @param CatalogProductView $catalogProductView
     * @param CatalogCategoryView $catalogCategoryView
     * @param Browser $browser
     * @param CustomerIndex $customerIndexPage
     * @param OrderCreateIndex $orderCreateIndex
     * @param CustomerInjectable $customer
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        FixtureFactory $fixtureFactory,
        AdminCache $adminCache,
        Widget $widget,
        CatalogProductView $catalogProductView,
        CatalogCategoryView $catalogCategoryView,
        Browser $browser,
        CustomerIndex $customerIndexPage,
        OrderCreateIndex $orderCreateIndex,
        CustomerInjectable $customer
    ) {
        // Flush cache
        $adminCache->open();
        $adminCache->getActionsBlock()->flushMagentoCache();
        $adminCache->getMessagesBlock()->waitSuccessMessage();

        $this->catalogProductView = $catalogProductView;
        $this->browser = $browser;
        $this->cmsIndex = $cmsIndex;
        $this->catalogCategoryView = $catalogCategoryView;
        $this->orderCreateIndex = $orderCreateIndex;
        $this->customerIndexPage = $customerIndexPage;
        $this->fixtureFactory = $fixtureFactory;
        $products = [];
        $customer->persist();

        $this->objectManager->create(
            'Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        )->run();

        $entities = $widget->getDataFieldConfig('widgetOptions')['source']->getEntities();
        foreach ($entities as $product) {
            $products[] = $product;
        }
        $this->openProducts($products);
        $this->checkRecentlyViewedBlockOnCategory($widget);

        $this->createOrderFromCustomerPage($customer);
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
     * Create order from customer page
     *
     * @param CustomerInjectable $customer
     * @return void
     */
    protected function createOrderFromCustomerPage(CustomerInjectable $customer)
    {
        $filter = ['email' => $customer->getEmail()];
        $this->customerIndexPage->open();
        $this->customerIndexPage->getCustomerGridBlock()->searchAndOpen($filter);

        $createOrderFromCustomer = $this->objectManager
            ->create('Magento\Customer\Test\TestStep\CreateOrderFromCustomerAccountStep');
        $createOrderFromCustomer->run();
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

        $products = $this->catalogCategoryView->getViewBlock()->getProductsFromRecentlyViewedBlock();
        \PHPUnit_Framework_Assert::assertTrue(
            in_array($widget->getWidgetOptions()[0]['entities'][0]->getName(), $products),
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
        $products = $this->orderCreateIndex->getCustomerActivitiesBlock()->getRecentlyViewedProductsBlock()
            ->getProductsFromRecentlyViewedBlock();
        \PHPUnit_Framework_Assert::assertTrue(
            in_array($widget->getWidgetOptions()[0]['entities'][0]->getName(), $products),
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
        return "Widget with type Recently Viewed Products is present on Order and Category pages.";
    }
}
