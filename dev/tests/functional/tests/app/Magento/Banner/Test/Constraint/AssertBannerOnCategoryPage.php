<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Constraint;

use Magento\Banner\Test\Fixture\BannerInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertBannerOnCategoryPage
 * Check that banner presents on specific category page
 */
class AssertBannerOnCategoryPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that banner presents on specific category page
     *
     * @param CatalogProductSimple $product
     * @param CmsIndex $cmsIndex
     * @param BannerInjectable $banner
     * @param CatalogCategoryView $catalogCategoryView
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerInjectable $customer[optional]
     * @return void
     */
    public function processAssert(
        CatalogProductSimple $product,
        CmsIndex $cmsIndex,
        BannerInjectable $banner,
        CatalogCategoryView $catalogCategoryView,
        CustomerAccountLogin $customerAccountLogin,
        CustomerInjectable $customer = null
    ) {
        $categoryName = $product->getCategoryIds()[0];
        $cmsIndex->open();
        if (!$cmsIndex->getLinksBlock()->isLinkVisible('Log Out') && $customer !== null) {
            $cmsIndex->getLinksBlock()->openLink("Log In");
            $customerAccountLogin->getLoginBlock()->login($customer);
        }
        $cmsIndex->getTopmenu()->selectCategoryByName($categoryName);
        \PHPUnit_Framework_Assert::assertTrue(
            $catalogCategoryView->getBannerViewBlock()->checkWidgetBanners($banner),
            'Banner is absent on Category page.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "Banner is presents on Category page.";
    }
}
