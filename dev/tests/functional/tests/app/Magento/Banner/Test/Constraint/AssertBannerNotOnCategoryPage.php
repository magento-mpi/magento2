<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Constraint;

use Magento\Banner\Test\Fixture\BannerInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertBannerNotOnCategoryPage
 * Check that banner is absent on specific category page
 */
class AssertBannerNotOnCategoryPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that banner is absent on specific category page
     *
     * @param CatalogProductSimple $product
     * @param CmsIndex $cmsIndex
     * @param BannerInjectable $banner
     * @param CatalogCategoryView $catalogCategoryView
     * @param CustomerInjectable|string $customer
     * @param CustomerAccountLogin $customerAccountLogin
     * @return void
     */
    public function processAssert(
        CatalogProductSimple $product,
        CmsIndex $cmsIndex,
        BannerInjectable $banner,
        CatalogCategoryView $catalogCategoryView,
        $customer,
        CustomerAccountLogin $customerAccountLogin
    ) {
        $categoryName = $product->getCategoryIds()[0];
        $cmsIndex->open();
        if (!$cmsIndex->getLinksBlock()->isLinkVisible('Log Out') && $customer instanceof CustomerInjectable) {
            $cmsIndex->getLinksBlock()->openLink("Log In");
            $customerAccountLogin->getLoginBlock()->login($customer);
        }
        $cmsIndex->getTopmenu()->selectCategoryByName($categoryName);
        \PHPUnit_Framework_Assert::assertFalse(
            $catalogCategoryView->getViewBlock()->checkWidgetBanners($banner),
            'Banner is presents on Category page.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "Banner is absent on Category page.";
    }
}
