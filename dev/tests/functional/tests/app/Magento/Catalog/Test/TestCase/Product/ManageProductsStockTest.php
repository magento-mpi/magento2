<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Mtf\Fixture\FixtureFactory;
use Mtf\ObjectManager;
use Mtf\Client\Browser;
use Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Test Creation for ManageProductsStock
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Set Configuration:
 *      - Display OutOfStock = Yes
 *      - Backorders - Allow Qty below = 0
 * 2. Create products according to dataSet
 *
 * Steps:
 * 1. Open product on frontend
 * 2. Add product to cart
 * 3. Perform all assertions
 *
 * @group Inventory_(MX)
 * @ZephyrId MAGETWO-29543
 */
class ManageProductsStockTest extends Injectable
{
    /**
     * Browser object
     *
     * @var Browser
     */
    protected $browser;

    /**
     * Catalog product view page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Setup configuration
     *
     * @param ObjectManager $objectManager
     * @return void
     */
    public function __prepare(ObjectManager $objectManager)
    {
        $setupConfigurationStep = $objectManager->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => "display_out_of_stock,backorders_allow_qty_below"]
        );
        $setupConfigurationStep->run();
    }

    /**
     * Injection data
     *
     * @param Browser $browser
     * @param CatalogProductView $catalogProductView
     * @return void
     */
    public function __inject(Browser $browser, CatalogProductView $catalogProductView)
    {
        $this->browser = $browser;
        $this->catalogProductView = $catalogProductView;
    }

    /**
     * Manage products stock
     *
     * @param CatalogProductSimple $product
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function test(CatalogProductSimple $product, FixtureFactory $fixtureFactory)
    {
        // Preconditions
        $product->persist();

        // Steps
        $this->browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $this->catalogProductView->getViewBlock()->addToCart($product);

        $cart['data']['items'] = ['products' => [$product]];
        return ['cart' => $fixtureFactory->createByCode('cart', $cart)];
    }

    /**
     * Set default configuration
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        $setupConfigurationStep = ObjectManager::getInstance()->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => "no_display_out_of_stock,backorders_no_backorders"]
        );
        $setupConfigurationStep->run();
    }
}
