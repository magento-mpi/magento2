<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Cms\Test\Fixture\CmsPage;
use Magento\Banner\Test\Fixture\Widget;
use Magento\Banner\Test\Fixture\BannerInjectable;
use Magento\CatalogRule\Test\Fixture\CatalogRule;
use Magento\Banner\Test\Page\Adminhtml\BannerNew;
use Magento\Banner\Test\Page\Adminhtml\BannerIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\SalesRule\Test\Fixture\SalesRuleInjectable;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;

/**
 * Test Creation for assign Related Cart and Catalog Rules to BannerEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Create customer
 * 2. Create CustomerSegment
 * 3. Create CMS Page
 * 4. Create widget type - Banner Rotator
 * 5. Create Shopping Cart Price Rule
 * 6. Create Catalog Price Rule
 * 7. Create banner
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Content->Banners
 * 3. Open created banner from preconditions
 * 4. Related Cart and Catalog Rules to banner
 * 5. Perform all assertions
 *
 * @group Banner_(PS)
 * @ZephyrId MAGETWO-27159
 */
class AssignRelatedPromotionsToBannerEntityTest extends Injectable
{
    /**
     * BannerIndex page
     *
     * @var BannerIndex
     */
    protected $bannerIndex;

    /**
     * BannerNew page
     *
     * @var BannerNew
     */
    protected $bannerNew;

    /**
     * Fixture Factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Inject pages
     *
     * @param BannerIndex $bannerIndex
     * @param BannerNew $bannerNew
     * @param CustomerAccountLogout $customerAccountLogout
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __inject(
        BannerIndex $bannerIndex,
        BannerNew $bannerNew,
        CustomerAccountLogout $customerAccountLogout,
        FixtureFactory $fixtureFactory
    ) {
        $this->customerAccountLogout = $customerAccountLogout;
        $this->bannerIndex = $bannerIndex;
        $this->bannerNew = $bannerNew;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Creation for assign Related Cart and Catalog Rules to BannerEntity test
     *
     * @param BannerInjectable $banner
     * @param CustomerInjectable|string $customer
     * @param CustomerSegment|string $customerSegment
     * @param CmsPage $cmsPage
     * @param string $catalogPriceRule
     * @param string $cartPriceRule
     * @param string $widget
     * @return array
     */
    public function test(
        BannerInjectable $banner,
        CmsPage $cmsPage,
        $catalogPriceRule,
        $cartPriceRule,
        $customer,
        $customerSegment,
        $widget
    ) {
        // Preconditions
        $customer = $this->createCustomer($customer);
        $customerSegment = $this->createCustomerSegment($customerSegment);
        $cmsPage->persist();
        $product = $this->createProduct();
        $banner = $this->createBanner($customerSegment, $banner);
        $this->createWidget($widget, $banner);

        $rules = $this->createRules($cartPriceRule, $catalogPriceRule);
        $filter = ['banner' => $banner->getName()];

        // Steps
        $this->bannerIndex->open();
        $this->bannerIndex->getGrid()->searchAndOpen($filter);
        $this->bannerNew->getBannerForm()->openTab('related_promotions');
        /** @var \Magento\Banner\Test\Block\Adminhtml\Banner\Edit\Tab\RelatedPromotions $tab */
        $tab = $this->bannerNew->getBannerForm()->getTabElement('related_promotions');
        if (!empty($rules['banner_sales_rules'])) {
            $tab->getCartPriceRulesGrid()->searchAndSelect(['id' => $rules['banner_sales_rules']]);
        }
        if (!empty($rules['banner_catalog_rules'])) {
            $tab->getCatalogPriceRulesGrid()->searchAndSelect(['id' => $rules['banner_catalog_rules']]);
        }
        $this->bannerNew->getPageMainActions()->save();

        return [
            'product' => $product,
            'banner' => $banner,
            'customer' => $customer,
            'customerSegment' => $customerSegment,
        ];
    }

    /**
     * Create Cart and Catalog Rules
     *
     * @param string $cartPriceRule
     * @param string $catalogPriceRule
     * @return array
     */
    protected function createRules($catalogPriceRule, $cartPriceRule)
    {
        $rules = [];
        if ($catalogPriceRule !== "-") {
            $catalogPriceRule = $this->fixtureFactory->createByCode('catalogRule', ['dataSet' => $catalogPriceRule]);
            $catalogPriceRule->persist();
            $rules['banner_catalog_rules'] = $catalogPriceRule->getId();
        }
        if ($cartPriceRule !== "-") {
            $cartPriceRule = $this->fixtureFactory->createByCode('salesRuleInjectable', ['dataSet' => $cartPriceRule]);
            $cartPriceRule->persist();
            $rules['banner_sales_rules'] = $cartPriceRule->getId();
        }

        return $rules;
    }

    /**
     * Create Customer
     *
     * @param string $customer
     * @return CustomerInjectable|null
     */
    protected function createCustomer($customer)
    {
        if ($customer !== '-') {
            $customer = $this->fixtureFactory->createByCode('customerInjectable', ['dataSet' => $customer]);
            $customer->persist();

            return $customer;
        }

        return null;
    }

    /**
     * Create Customer Segment
     *
     * @param string $customerSegment
     * @return CustomerSegment|null
     */
    protected function createCustomerSegment($customerSegment)
    {
        if ($customerSegment !== '-') {
            $customerSegment = $this->fixtureFactory->createByCode('customerSegment', ['dataSet' => $customerSegment]);
            $customerSegment->persist();

            return $customerSegment;
        }

        return null;
    }

    /**
     * Create Product
     *
     * @return CatalogProductSimple
     */
    protected function createProduct()
    {
        $product = $this->fixtureFactory->createByCode('catalogProductSimple', ['dataSet' => 'product_with_category']);
        $product->persist();

        return $product;
    }

    /**
     * Create banner
     *
     * @param BannerInjectable $banner
     * @param CustomerSegment|string $customerSegment
     * @return BannerInjectable
     */
    protected function createBanner($customerSegment, BannerInjectable $banner)
    {
        if ($customerSegment !== null) {
            $banner = $this->fixtureFactory->createByCode(
                'bannerInjectable',
                [
                    'dataSet' => 'default',
                    'data' => [
                        'customer_segment_ids' => [$customerSegment->getSegmentId()],
                    ]
                ]
            );
        }
        $banner->persist();

        return $banner;
    }

    /**
     * Create Widget
     *
     * @param string $widget
     * @param BannerInjectable $banner
     * @return Widget
     */
    protected function createWidget($widget, BannerInjectable $banner)
    {
        $widget = $this->fixtureFactory->create(
            '\Magento\Banner\Test\Fixture\Widget',
            [
                'dataSet' => $widget,
                'data' => [
                    'parameters' => [
                        'banner_ids' => $banner->getBannerId()
                    ],
                ]
            ]
        );
        $widget->persist();

        return $widget;
    }

    /**
     * Logout customer from frontend account
     *
     * @return void
     */
    public function tearDown()
    {
        $this->customerAccountLogout->open();
    }
}
