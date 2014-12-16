<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Tax\Test\TestCase;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\SalesRule\Test\Fixture\SalesRuleInjectable;
use Magento\Tax\Test\Fixture\TaxRule;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

/**
 * Test Flow:
 *
 * 1. Log in as default admin user.
 * 2. Go to Stores > Taxes > Tax Rules.
 * 3. Click 'Add New Tax Rule' button.
 * 4. Assign default rates to rule.
 * 5. Save Tax Rate.
 * 6. Go to Products > Catalog.
 * 7. Add new product.
 * 8. Fill data according to dataset.
 * 9. Save product.
 * 10. Go to Stores > Configuration.
 * 11. Fill Tax configuration according to data set.
 * 12. Save tax configuration.
 * 13. Perform all assertions.
 *
 * @group Tax_(CS)
 * @ZephyrId MAGETWO-27809
 */
class TaxCalculationTest extends Injectable
{
    /**
     * Catalog product page.
     *
     * @var CheckoutCart
     */
    protected $checkoutCart;

    /**
     * Cms index page.
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Customer login page.
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Fixture customer.
     *
     * @var CustomerInjectable
     */
    protected $customer;

    /**
     * Fixture SalesRule.
     *
     * @var SalesRuleInjectable
     */
    protected $salesRule;

    /**
     * Sales Rule Id.
     *
     * @var array
     */
    public static $salesRuleName;

    /**
     * Tax Rule Id.
     *
     * @var array
     */
    public static $taxRuleCode;

    /**
     * Skip failed tests.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::markTestIncomplete("Epic: MAGETWO-30073");
    }

    /**
     * Prepare data.
     *
     * @param FixtureFactory $fixtureFactory
     * @param SalesRuleInjectable $salesRule
     * @return void
     */
    public function __prepare(
        FixtureFactory $fixtureFactory,
        SalesRuleInjectable $salesRule
    ) {
        $this->fixtureFactory = $fixtureFactory;
        $customer = $fixtureFactory->createByCode('customerInjectable', ['dataSet' => 'johndoe_unique']);
        $customer->persist();
        $this->customer = $customer;
        $salesRule->persist();
        $this->salesRule = $salesRule;
        self::$salesRuleName = $salesRule->getName();
    }

    /**
     * Injection data.
     *
     * @param CmsIndex $cmsIndex
     * @param CheckoutCart $checkoutCart
     * @param CustomerAccountLogin $customerAccountLogin
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CheckoutCart $checkoutCart,
        CustomerAccountLogin $customerAccountLogin
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->checkoutCart = $checkoutCart;
        $this->customerAccountLogin = $customerAccountLogin;
    }

    /**
     * Login customer.
     *
     * @return void
     */
    protected function loginCustomer()
    {
        if (!$this->cmsIndex->getLinksBlock()->isLinkVisible('Log Out')) {
            $this->cmsIndex->getLinksBlock()->openLink("Log In");
            $this->customerAccountLogin->getLoginBlock()->login($this->customer);
        }
    }

    /**
     * Clear shopping cart.
     *
     * @return void
     */
    protected function clearShoppingCart()
    {
        $this->checkoutCart->open();
        $this->checkoutCart->getCartBlock()->clearShoppingCart();
    }

    /**
     * Test product prices with tax.
     *
     * @param CatalogProductSimple $product
     * @param TaxRule $taxRule
     * @param string $configData
     * @return array
     */
    public function test(CatalogProductSimple $product, TaxRule $taxRule, $configData)
    {
        //Preconditions
        $this->objectManager->create('Magento\Core\Test\TestStep\SetupConfigurationStep', ['configData' => $configData])
            ->run();
        $product->persist();
        $taxRule->persist();
        self::$taxRuleCode = $taxRule->getData()['code'];
        //Steps
        $this->cmsIndex->open();
        $this->loginCustomer($this->customer);
        $this->clearShoppingCart();
    }

    /**
     * Tear down after each test.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->objectManager->create('Magento\Tax\Test\TestStep\DeleteAllTaxRulesStep')->run();
        $this->objectManager->create('Magento\SalesRule\Test\TestStep\DeleteAllSalesRuleStep')->run();
        $this->objectManager->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'default_tax_configuration,shipping_tax_class_taxable_goods_rollback']
        )->run();
    }
}
