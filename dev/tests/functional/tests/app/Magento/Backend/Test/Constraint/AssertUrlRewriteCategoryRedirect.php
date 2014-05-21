<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;
use Magento\Backend\Test\Fixture\UrlRewriteCategory;
use Magento\Catalog\Test\Fixture\CatalogCategoryEntity;

/**
 * Class AssertUrlRewriteCategoryRedirect
 * Assert check URL rewrite category redirect
 */
class AssertUrlRewriteCategoryRedirect extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert check URL rewrite category redirect
     *
     * @param UrlRewriteCategory $urlRewriteCategory
     * @param CatalogCategoryEntity $category
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        UrlRewriteCategory $urlRewriteCategory,
        CatalogCategoryEntity $category,
        Browser $browser
    ) {
        $browser->open($_ENV['app_frontend_url'] . $urlRewriteCategory->getRequestPath());
        $url = strtolower($category->getName());
        \PHPUnit_Framework_Assert::assertEquals(
            $browser->getUrl(),
            $_ENV['app_frontend_url'] . $url . '.html',
            'URL rewrite category redirect false.'
            . "\nExpected: " . $_ENV['app_frontend_url'] . $url . '.html'
            . "\nActual: " . $browser->getUrl()
        );
    }

    /**
     * URL rewrite category redirect success
     *
     * @return string
     */
    public function toString()
    {
        return 'URL rewrite category redirect success.';
    }
}
