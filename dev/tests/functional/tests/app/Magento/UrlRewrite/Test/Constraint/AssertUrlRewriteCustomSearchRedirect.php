<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;
use Magento\UrlRewrite\Test\Fixture\UrlRewrite;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Class AssertUrlRewriteCustomSearchRedirect
 * Assert that product was found on search page
 */
class AssertUrlRewriteCustomSearchRedirect extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created entity was found on search page
     *
     * @param UrlRewrite $initialRewrite
     * @param UrlRewrite $urlRewrite
     * @param Browser $browser
     * @param CatalogCategoryView $categoryView
     * @return void
     */
    public function processAssert(
        UrlRewrite $initialRewrite,
        UrlRewrite $urlRewrite,
        Browser $browser,
        CatalogCategoryView $categoryView
    ) {
        $urlRequestPath = $urlRewrite->hasData('request_path')
            ? $urlRewrite->getRequestPath()
            : $initialRewrite->getRequestPath();
        $browser->open($_ENV['app_frontend_url'] . $urlRequestPath);
        $entity = $urlRewrite->getDataFieldConfig('target_path')['source']->getEntity()->getName();

        \PHPUnit_Framework_Assert::assertTrue(
            $categoryView->getListProductBlock()->isProductVisible($entity),
            "Created entity '{$entity}' isn't found."
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is found on search page.';
    }
}
