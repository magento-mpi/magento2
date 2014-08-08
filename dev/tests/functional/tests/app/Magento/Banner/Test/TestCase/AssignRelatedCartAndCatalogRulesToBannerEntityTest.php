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
use Magento\Banner\Test\Fixture\BannerInjectable;
use Magento\CatalogRule\Test\Fixture\CatalogRule;
use Magento\Banner\Test\Page\Adminhtml\BannerNew;
use Magento\Banner\Test\Page\Adminhtml\BannerIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
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
 * @group CMS_Content_(PS)
 * @ZephyrId MAGETWO-27159
 */
class AssignRelatedCartAndCatalogRulesToBannerEntityTest extends Injectable
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
     * Inject pages
     *
     * @param BannerIndex $bannerIndex
     * @param BannerNew $bannerNew
     * @return void
     */
    public function __inject(BannerIndex $bannerIndex, BannerNew $bannerNew)
    {
        $this->bannerIndex = $bannerIndex;
        $this->bannerNew = $bannerNew;
    }

    /**
     * Creation for assign Related Cart and Catalog Rules to BannerEntity test
     *
     * @param FixtureFactory $fixtureFactory
     * @param BannerInjectable $banner
     * @param CustomerInjectable|string $customer
     * @param CustomerSegment|string $customerSegment
     * @param CmsPage $cmsPage
     * @param CatalogRule $catalogPriceRule
     * @param SalesRuleInjectable $cartPriceRule
     * @param string $isCatalogPriceRule
     * @param string $isCartPriceRule
     * @param string $widget
     * @return array
     */
    public function test(
        FixtureFactory $fixtureFactory,
        BannerInjectable $banner,
        CmsPage $cmsPage,
        CatalogRule $catalogPriceRule,
        SalesRuleInjectable $cartPriceRule,
        $customer,
        $customerSegment,
        $isCatalogPriceRule,
        $isCartPriceRule,
        $widget
    ) {
        // Precondition
        if ($customer !== '-') {
            $customer = $fixtureFactory->createByCode('customerInjectable', ['dataSet' => $customer]);
            $customer->persist();
        }
        if ($customerSegment !== '-') {
            $customerSegment = $fixtureFactory->createByCode('customerSegment', ['dataSet' => $customerSegment]);
            $customerSegment->persist();
        }
        $cmsPage->persist();
        /**@var CatalogProductSimple $catalogProductSimple */
        $product = $fixtureFactory->createByCode(
            'catalogProductSimple',
            ['dataSet' => 'product_with_category']
        );
        $product->persist();

        if ($customerSegment instanceof CustomerSegment) {
            $banner = $fixtureFactory->createByCode(
                'bannerInjectable',
                [
                    'dataSet' => 'default',
                    'data' => [
                        'customer_segment_ids' => [$customerSegment->getSegmentId()],
                    ]
                ]
            );
            $banner->persist();
        } else {
            $banner->persist();
        }
        $widget = $fixtureFactory->createByCode(
            'widget',
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
        $rules = $this->createRules($isCatalogPriceRule, $isCartPriceRule, $cartPriceRule, $catalogPriceRule);
        $filter = ['banner' => $banner->getName()];

        // Steps
        $this->bannerIndex->open();
        $this->bannerIndex->getGrid()->searchAndOpen($filter);
        $this->bannerNew->getBannerForm()->openTab('related_promotions');
        if (!empty($rules['banner_sales_rules'])) {
            $this->bannerNew->getCartPriceRulesGrid()->searchAndSelect(['id' => $rules['banner_sales_rules']]);
        }
        if (!empty($rules['banner_catalog_rules'])) {
            $this->bannerNew->getCatalogPriceRulesGrid()->searchAndSelect(['id' => $rules['banner_catalog_rules']]);
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
     * @param string $isCatalogPriceRule
     * @param string $isCartPriceRule
     * @param SalesRuleInjectable $cartPriceRule
     * @param CatalogRule $catalogPriceRule
     * @return array
     */
    protected function createRules(
        $isCatalogPriceRule,
        $isCartPriceRule,
        SalesRuleInjectable $cartPriceRule,
        CatalogRule $catalogPriceRule
    ) {
        if ($isCatalogPriceRule === "Yes") {
            $catalogPriceRule->persist();
        }
        if ($isCartPriceRule === "Yes") {
            $cartPriceRule->persist();
        }
        $rules['banner_catalog_rules'] = $catalogPriceRule->hasData('id') ? $catalogPriceRule->getId() : "";
        $rules['banner_sales_rules'] = $cartPriceRule->hasData('id') ? $cartPriceRule->getId() : "";

        return $rules;
    }
}
