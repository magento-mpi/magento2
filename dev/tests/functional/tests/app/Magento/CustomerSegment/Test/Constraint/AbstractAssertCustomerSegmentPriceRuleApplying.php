<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Constraint;

use Mtf\Client\Browser;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentIndex;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentNew;
use Magento\Checkout\Test\Page\CheckoutCart;
use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Class AssertCartPriceRuleApplying
 * Abstract class for implementing assert applying
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractAssertCustomerSegmentPriceRuleApplying extends AbstractConstraint
{
    /**
     * Page CheckoutCart
     *
     * @var CheckoutCart
     */
    protected $checkoutCart;

    /**
     * Page CmsIndex
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Page CustomerAccountLogin
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * Page CustomerAccountLogout
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Page CatalogProductView
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Customer from precondition
     *
     * @var CustomerInjectable
     */
    protected $customer;

    /**
     * Customer segment fixture
     *
     * @var CustomerSegment
     */
    protected $customerSegment;

    /**
     * Customer Segment index page
     *
     * @var CustomerSegmentIndex
     */
    protected $customerSegmentIndex;

    /**
     * Page for creating new customer
     *
     * @var CustomerSegmentNew
     */
    protected $customerSegmentNew;

    /**
     * Implementation assert
     *
     * @return void
     */
    abstract protected function assert();

    /**
     * Login to frontend. Create product. Adding product to shopping cart
     *
     * @param CheckoutCart $checkoutCart
     * @param CatalogProductSimple $product
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountLogout $customerAccountLogout
     * @param CatalogProductView $catalogProductView
     * @param CustomerInjectable $customer
     * @param CustomerSegment $customerSegment
     * @param CustomerSegmentIndex $customerSegmentIndex
     * @param Browser $browser
     * @param CustomerSegmentNew $customerSegmentNew
     * @return void
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function processAssert(
        CheckoutCart $checkoutCart,
        CatalogProductSimple $product,
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountLogout $customerAccountLogout,
        CatalogProductView $catalogProductView,
        CustomerInjectable $customer,
        CustomerSegment $customerSegment,
        CustomerSegmentIndex $customerSegmentIndex,
        Browser $browser,
        CustomerSegmentNew $customerSegmentNew
    ) {
        $this->checkoutCart = $checkoutCart;
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customerAccountLogout = $customerAccountLogout;
        $this->catalogProductView = $catalogProductView;
        $this->customer = $customer;
        $this->customerSegment = $customerSegment;
        $this->customerSegmentIndex = $customerSegmentIndex;
        $this->customerSegmentNew = $customerSegmentNew;

        $this->cmsIndex->open();
        $this->loginCustomer();

        $product->persist();
        $this->checkoutCart->open()->getCartBlock()->clearShoppingCart();
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $this->catalogProductView->getViewBlock()->clickAddToCart();
        $this->checkoutCart->getMessagesBlock()->getSuccessMessages();
        $this->assert();
        $this->customerAccountLogout->open();
    }

    /**
     * Login customer
     *
     * @return void
     */
    protected function loginCustomer()
    {
        $loginCustomerOnFrontendStep = $this->objectManager->create(
            'Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $this->customer]
        );
        $loginCustomerOnFrontendStep->run();
    }
}
