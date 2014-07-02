<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Constraint;

use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;
use Magento\UrlRewrite\Test\Fixture\UrlRewrite;

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
     * Assert that product was found on search page
     *
     * @param UrlRewrite $urlRewrite
     * @param UrlRewrite $rewrite
     * @param Browser $browser
     * @param CatalogCategoryView $categoryView
     * @return void
     */
    public function processAssert(
        UrlRewrite $urlRewrite,
        UrlRewrite $rewrite,
        Browser $browser,
        CatalogCategoryView $categoryView
    ) {
        $browser->open($_ENV['app_frontend_url'] . $urlRewrite->getRequestPath());
        $product = $rewrite->getDataFieldConfig('id_path')['source']->getEntity()->getName();

        \PHPUnit_Framework_Assert::assertTrue(
            $categoryView->getListProductBlock()->isProductVisible($product),
            "Product '{$product}' isn't found."
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
